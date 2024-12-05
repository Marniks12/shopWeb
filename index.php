<?php

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
