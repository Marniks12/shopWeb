<?php
require_once 'session.php'; // Zorg dat de databaseklasse is inbegrepen
require_once  'User.php';
if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%'; // Voeg wildcards toe voor LIKE-query

    try {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("SELECT id, title, description, price FROM products WHERE title LIKE :search OR description LIKE :search");
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results); // JSON-uitvoer voor gebruik in JavaScript
    } catch (PDOException $e) {
        echo json_encode([]); // Stuur een lege array als er een fout optreedt
    }
} else {
    echo json_encode([]); // Geen zoekterm, stuur een lege array
}
