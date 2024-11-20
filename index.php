<?php
session_start();

// Check if the user is logged in
if ($_SESSION['loggedin'] !== true) {
    header('Location: login.php');
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
    $stmt = $conn->prepare("SELECT title, price, img FROM products");
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
    <h1>Welcome to Soundscape</h1>
    <div class="producten">

        <!-- Display products -->
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <article>
                    <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product" />
                    <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                    <h1> €  <?php echo htmlspecialchars($product['price']); ?></h1>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
