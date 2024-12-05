<?php
$host = 'localhost';
$dbname = 'webshop1';
$username = 'root';
$password = '';

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Zoek query
        $stmt = $conn->prepare("SELECT id, name, description FROM products WHERE name LIKE :query OR description LIKE :query");
        $stmt->execute(['query' => '%' . $query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

