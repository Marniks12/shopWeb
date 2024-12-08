<!-- Admin-functionaliteiten toevoegen -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<h2>Beheer productgegevens</h2>
</body>
</html>
<?php
require_once 'session.php';
require 'User.php'; // Laad de Db-klasse

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $product_id = $data['product_id'];
    $comment = $data['comment'];
    $user_id = $_SESSION['user_id']; // Haal de gebruiker op uit de sessie

    try {
        // Maak verbinding via de Db-klasse
        $conn = Db::getConnection(); // Verbinding maken via Db-klasse

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
