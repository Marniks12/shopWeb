<?php 
require_once 'session.php'; // Zorg ervoor dat je sessie is gestart
require_once 'User.php'; // Zorg ervoor dat je de User klasse laadt
 // Gebruik de Db-klasse

require_once 'User.php';


// Logout functionaliteit
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    User::logout(); // Roep de logout-functie aan
    header('Location: login.php'); // Optionele redirect na logout
    exit;
}

// Winkelwagen logica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controleer of de actie 'add_to_cart' is
    if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart' && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Controleer of het product al in de winkelwagen zit
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity; // Voeg toe aan bestaande hoeveelheid
        } else {
            $_SESSION['cart'][$product_id] = $quantity; // Voeg nieuw product toe
        }

        // Redirect terug naar de winkelwagenpagina
        header('Location: cart.php');
        exit;
    }

    // Verwerk andere POST acties (product toevoegen, bijwerken, verwijderen)
    if (isset($_POST['add_product'])) {
        // Product toevoegen
        $stmt = $conn->prepare("INSERT INTO products (title, price, img, categorie_id, description) 
                                VALUES (:title, :price, :img, :categorie_id, :description)");
        $stmt->execute([
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'img' => $_POST['img'],
            'categorie_id' => $_POST['categorie_id'],
            'description' => $_POST['description']
        ]);
        echo "<p>Product succesvol toegevoegd!</p>";
    }

    if (isset($_POST['update_product'])) {
        // Product bijwerken
        $stmt = $conn->prepare("UPDATE products SET title = :title, price = :price, img = :img, categorie_id = :categorie_id, description = :description
                                WHERE id = :product_id");
        $stmt->execute([
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'img' => $_POST['img'],
            'categorie_id' => $_POST['categorie_id'],
            'description' => $_POST['description'],
            'product_id' => $_POST['product_id']
        ]);
        echo "<p>Product succesvol bijgewerkt!</p>";
    }

    if (isset($_POST['delete_product'])) {
        // Product verwijderen
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :product_id");
        $stmt->execute(['product_id' => $_POST['product_id']]);
        echo "<p>Product succesvol verwijderd!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="admin.css">
    <title>Webshop</title>
</head>
<body>

<?php include 'header.php'; ?> <!-- Header wordt hier ingeladen -->

<!-- Filters -->
<div class="filters">
    <h2><a href="index.php?category=headphones">Headphones</a></h2>
    <h2><a href="index.php?category=speakers">Speakers</a></h2>
    <h2><a href="index.php?category=earbuds">Earbuds</a></h2>
</div>

<?php 
// Maak een verbinding met de database
try {
    $conn = Db::getConnection();

    // Haal de geselecteerde categorie op uit de URL (indien aanwezig)
    $selected_category = isset($_GET['category']) ? $_GET['category'] : null;

    // Producten query op basis van de geselecteerde categorie
    if ($selected_category) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE categorie_id = :category");
        $stmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * FROM products");
        $stmt->execute();
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Er is een probleem met de databaseverbinding: " . $e->getMessage();
    die(); // Stop de uitvoering bij een fout
}

// Controleer of de gebruiker een admin is
$isAdmin = isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin'; 
?>

<?php if ($isAdmin): ?>
    <!-- Admin Functionaliteiten -->
    <section class="admin-actions">
        <h2>Admin Area</h2>
        <!-- Product toevoegen -->
        <section class="add-product">
            <h3>Nieuw product toevoegen</h3>
            <form method="POST" action="index.php" class="product-form">
                <input type="text" name="title" placeholder="Productnaam" required>
                <input type="number" name="price" step="0.01" placeholder="Prijs (€)" required>
                <input type="url" name="img" placeholder="Afbeeldings-URL" required>
                <input type="text" name="categorie_id" placeholder="Categorie-ID">
                <input type="text" name="description" placeholder="Beschrijving">
                <button type="submit" name="add_product" class="btn">Product Toevoegen</button>
            </form>
        </section>
        <!-- Product bijwerken en verwijderen -->
        <?php foreach ($products as $product): ?>
            <section class="update-product">
                <h3>Product: <?php echo htmlspecialchars($product['title']); ?></h3>
                <!-- Verwijder product -->
                <form method="POST" action="index.php" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">
                    <input type="hidden" name="delete_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn delete">Verwijderen</button>
                </form>
                <!-- Bijwerken van product -->
                <form method="POST" action="index.php" class="update-form">
                    <input type="hidden" name="update_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                    <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    <input type="url" name="img" value="<?php echo htmlspecialchars($product['img']); ?>" required>
                    <input type="text" name="categorie_id" value="<?php echo htmlspecialchars($product['categorie_id'] ?? ''); ?>">
                    <input type="text" name="description" value="<?php echo htmlspecialchars($product['description'] ?? ''); ?>">
                    <button type="submit" class="btn">Bijwerken</button>
                </form>
            </section>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<!-- Producten weergave voor alle gebruikers -->
<div class="producten">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <a href="detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" style="text-decoration: none; color: inherit;">
                <article>
                    <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product" />
                    <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                    <h1>€ <?php echo htmlspecialchars($product['price']); ?></h1>
                    <!-- Winkelwagen formulier met actieparameter -->
                    <form method="POST" action="index.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add_to_cart"> <!-- Voeg actieparameter toe -->
                        <button type="submit">Voeg toe aan winkelmandje</button>
                    </form>
                </article>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products available at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
