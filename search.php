<?php
require_once 'session.php'; // Zorg ervoor dat de sessie is gestart
require_once 'User.php'; // Zorg ervoor dat de Db-klasse is geladen

// Verwerk zoekopdrachten en haal producten op
try {
    // Maak verbinding met de database via de Db-klasse
    $conn = Db::getConnection();

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        // Zoekterm toevoegen voor LIKE query
        $search = "%" . $_GET['search'] . "%";
        
        // Zoek in de kolommen title, price, en description
        $stmt = $conn->prepare("SELECT id, title, price FROM products WHERE title LIKE :search OR description LIKE :search OR price LIKE :search");
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);  // Bind de zoekterm voor de LIKE query
    } else {
        // Als er geen zoekopdracht is, haal alle producten op
        $stmt = $conn->prepare("SELECT id, title, price FROM products");
    }

    // Voer de query uit
    $stmt->execute();
    
    // Haal alle resultaten op
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Foutafhandeling bij databaseproblemen
    echo "Verbinding mislukt: " . $e->getMessage();
}
?>
