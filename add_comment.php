<?php
require_once 'session.php';
require 'User.php';  // Laad de Db-klasse voor de databaseverbinding

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $product_id = $data['product_id'];
    $comment = $data['comment'];
    $user_id = $_SESSION['user_id']; // Haal de gebruiker op uit de sessie

    // Verkrijg de databaseverbinding via de Db-klasse
    try {
        $conn = Db::getConnection();

        // Voeg het commentaar toe aan de database
        $stmt = $conn->prepare("
            INSERT INTO comments (product_id, user_id, comment, created_at) 
            VALUES (:product_id, :user_id, :comment, NOW())
        ");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
