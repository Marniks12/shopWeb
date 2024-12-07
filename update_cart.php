<?php
require_once 'session.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Verkrijg product_id en nieuwe hoeveelheid
    $product_id = $data['product_id'];
    $quantity = $data['quantity'];

    // Update de sessie met de nieuwe hoeveelheid
    $_SESSION['cart'][$product_id] = $quantity;

    // Haal productgegevens op uit de database
    $host = 'localhost';
    $dbname = 'webshop1';
    $username = 'root';
    $password = '';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Haal productgegevens op
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = :id");
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $new_total = $product['price'] * $quantity;

            // Bereken de nieuwe subtotaal van de winkelwagen
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $id => $qty) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($prod) {
                    $subtotal += $prod['price'] * $qty;
                }
            }

            // Stuur de gegevens terug naar de frontend
            echo json_encode([
                'success' => true,
                'currency_unit' => '€', // Pas dit aan naar je currency unit
                'new_total' => $new_total,
                'subtotal' => $subtotal
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
