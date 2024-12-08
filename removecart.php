<?php
require_once 'session.php';
require_once 'User.php'; // Laad de Db-klasse

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Verwijder het product uit de winkelwagen in de sessie
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Verbind met de database als je dat in de toekomst nodig hebt (bijvoorbeeld voor logging of extra validatie)
    try {
        $conn = Db::getConnection(); // Verbinding maken via de Db-klasse

        // Eventueel kun je hier iets met de database doen, bijvoorbeeld het loggen van de verwijdering
        // Voor nu is er geen directe database-interactie nodig, omdat we enkel de sessie bijwerken.
        
    } catch (PDOException $e) {
        echo "Databasefout: " . $e->getMessage();
    }

    // Redirect naar de winkelwagenpagina
    header('Location: cart.php');
    exit;
}
?>
