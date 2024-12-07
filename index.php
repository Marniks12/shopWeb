<?php 
require_once 'session.php';

require_once 'User.php'; // Zorg ervoor dat je de User klasse laadt


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    User::logout(); // Roep de logout-functie aan
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

// Redirect terug naar de productpagina
header('Location: cart.php');
exit;
}

// Database connection details
$host = 'localhost'; 
$dbname = 'webshop1';
$username = 'root'; 
$password = ''; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    echo "Connection failed: " . $e->getMessage();
}

// Verwerk de formulieracties (product toevoegen, bijwerken, verwijderen)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Product toevoegen
        $title = $_POST['title'];
        $price = $_POST['price'];
        $img = $_POST['img'];
        $categorie_id = $_POST['categorie_id'];
        $description = $_POST['description'];

        try {
            $stmt = $conn->prepare("INSERT INTO products (title, price, img, categorie_id, description) 
                                    VALUES (:title, :price, :img, :categorie_id, :description)");
            $stmt->execute([
                'title' => $title,
                'price' => $price,
                'img' => $img,
                'categorie_id' => $categorie_id,
                'description' => $description
            ]);
            echo "<p>Product succesvol toegevoegd!</p>";
        } catch (PDOException $e) {
            echo "Fout bij het toevoegen van het product: " . $e->getMessage();
        }
    }

    if (isset($_POST['update_product'])) {
        // Product bijwerken
        $product_id = $_POST['product_id'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $img = $_POST['image_url'];
        $categorie_id = $_POST['categorie_id'];
        $description = $_POST['description'];

        try {
            $stmt = $conn->prepare("UPDATE products SET title = :title, price = :price, img = :img, categorie_id = :categorie_id, description = :description
                                    WHERE id = :product_id");
            $stmt->execute([
                'title' => $title,
                'price' => $price,
                'img' => $img,
                'categorie_id' => $categorie_id,
                'description' => $description,
                'product_id' => $product_id
            ]);
            echo "<p>Product succesvol bijgewerkt!</p>";
        } catch (PDOException $e) {
            echo "Fout bij het bijwerken van het product: " . $e->getMessage();
        }
    }

    if (isset($_POST['delete_product'])) {
        // Product verwijderen
        $product_id = $_POST['product_id'];

        try {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = :product_id");
            $stmt->execute(['product_id' => $product_id]);
            echo "<p>Product succesvol verwijderd!</p>";
        } catch (PDOException $e) {
            echo "Fout bij het verwijderen van het product: " . $e->getMessage();
        }
    }
}
?> 




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Webshop</title>
</head>
<body>

<?php include 'header.php'; ?> <!-- Header wordt hier ingeladen -->
    
<div class="filters">
    <h2><a href="index.php?category=headphones">Headphones</a></h2>
    <h2><a href="index.php?category=speakers">Speakers</a></h2>
    <h2><a href="index.php?category=earbuds">Earbuds</a></h2>
</div>

<div class="producten">

        <!-- Display products -->
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <a href="detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" style="text-decoration: none; color: inherit;">
        
    <article>
    
        <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product" />
       
        <h2><?php echo htmlspecialchars($product['title']); ?>
    
        <form method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit">Voeg toe aan winkelmandje</button>
            </form>
    </h2>
        <h1>€ <?php echo htmlspecialchars($product['price']); ?></h1>
       
    </article>
</a>

            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>

