<?php
// Start de sessie
require_once 'session.php';


// Databaseverbinding
$host = 'localhost'; // Je MySQL server
$dbname = 'webshop1'; // Je database naam
$username = 'root'; // Je MySQL username
$password = ''; // Je MySQL wachtwoord

try {
    // Maak verbinding met de database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Zet PDO foutmodi op uitzondering
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    die('Je moet ingelogd zijn om een commentaar toe te voegen.');
}

// Haal de gebruiker ID op uit de sessie
$user_id = $_SESSION['user_id'];  // Gebruik 'user_id' hier

// Controleer of de commentaar is verzonden via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'], $_POST['product_id'])) {
    $comment = $_POST['comment'];
    $product_id = $_POST['product_id'];

    // Voeg het commentaar toe aan de database
    try {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, product_id, comment) VALUES (:user_id, :product_id, :comment)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // De gebruikers-ID van de sessie
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT); // Het product-ID
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); // Het commentaar zelf
        $stmt->execute();
        
        // Redirect naar de productpagina
        header("Location: detail.php?id=" . $product_id);
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Error: Comment or product ID not set.";
}
?> 