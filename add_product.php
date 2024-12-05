<?php
session_start();
require 'Product.php';

// Controleer of de gebruiker admin is
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Maak een databaseverbinding
$host = 'localhost';
$dbname = 'webshop1';
$username = 'root';
$password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verwerk het formulier
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $price = $_POST['price'];
        $img = $_FILES['img'];

        $product = new Product($conn); // Maak een Product-object

        try {
            $product->addProduct($title, $price, $img); // Voeg product toe
            $successMessage = "Product succesvol toegevoegd!";
        } catch (Exception $e) {
            $errorMessage = $e->getMessage(); // Toon foutmelding
        }
    }
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Product Toevoegen</title>
</head>
<body>
    <h1>Voeg een nieuw product toe</h1>

    <?php if (isset($successMessage)): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php elseif (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="price">Prijs:</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required>
        <br>
        <label for="img">Afbeelding:</label>
        <input type="file" id="img" name="img" accept="image/*" required>
        <br>
        <button type="submit">Product toevoegen</button>
    </form>
</body>
</html>
