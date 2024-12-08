<?php
require_once 'session.php';
require_once 'User.php';
 // Gebruik de Db-klasse
include_once 'user_info.php'; // Laadt het bestand waar de gebruikersnaam wordt opgehaald

// Voeg het product toe aan de winkelwagen
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

    // Redirect terug naar de winkelwagenpagina
    header('Location: cart.php');
    exit;
}

try {
    // Maak verbinding met de database via Db-klasse
    $conn = Db::getConnection();

    // Haal producten op uit de database
    $stmt = $conn->prepare("SELECT title, price, img, id FROM products");
    $stmt->execute();

    // Fetch alle producten als een associatieve array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Databaseverbinding mislukt: " . $e->getMessage();
    exit;
}

// Controleer of een ID is doorgegeven
if (!isset($_GET['id'])) {
    exit("Product niet gevonden.");
}

// Haal productdetails en commentaren op uit de database
try {
    // Haal het product op op basis van de ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit("Product niet gevonden.");
    }

    // Haal commentaren op voor dit product
    $commentStmt = $conn->prepare("
        SELECT c.comment, u.email, c.created_at
        FROM comments c
        JOIN inlog u ON c.user_id = u.id
        WHERE c.product_id = :product_id
        ORDER BY c.created_at DESC
    ");
    $commentStmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_INT);
    $commentStmt->execute();
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Databaseverbinding mislukt: " . $e->getMessage();
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="detail.css">
    <title><?php echo htmlspecialchars($product['title']); ?></title>
    
</head>
<body>
    <?php include 'header.php'; ?> <!-- Header wordt hier ingeladen -->

    <div class="product-container">
        <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product Image" class="product-image">
    </div>

    <div class="product-info">
        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
        <p class="product-description">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum.
        </p>
        <p class="product-price">€ <?php echo htmlspecialchars($product['price']); ?></p>
        <a href="index.php" class="back-button">Back to Products</a>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit">Voeg toe aan winkelmandje</button>
        </form>
    </div>
    <div id="comments">
    <h3>Reacties:</h3>
    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['email']); ?></strong> schreef:</p>
                <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                <p><small>Op <?php echo htmlspecialchars($comment['created_at']); ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Er zijn nog geen reacties voor dit product.</p>
    <?php endif; ?>
</div>

  <script src="comment.js"></script>
    
</body>
</html>