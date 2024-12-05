<?php

include_once 'user_info.php';  // Laadt het bestand waar de gebruikersnaam wordt opgehaald

session_start(); // Start de sessie

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

// Redirect terug naar de productpagina
header('Location: cart.php');
exit;
}

// Database connection details
$host = 'localhost'; // Your MySQL server
$dbname = 'webshop1'; // Your database name
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL password

try {
    // Create a PDO instance (connect to the database)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch products from the database
    $stmt = $conn->prepare("SELECT title, price, img, id FROM products");
    $stmt->execute();

    // Fetch all products as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


// Controleer of een ID is doorgegeven
if (!isset($_GET['id'])) {
    exit("Product not found.");
}

// Databaseverbinding maken
$host = 'localhost';
$dbname = 'webshop1';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Haal het product op op basis van de ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    // Productgegevens ophalen
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        exit("Product not found.");
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
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
</body>
</html>
