<?php
require_once 'session.php';
require 'User.php'; // Zorg ervoor dat de Db-klasse is ingeladen

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

// Haal de email van de ingelogde gebruiker
$userEmail = $_SESSION['email'];

// Maak verbinding via de Db-klasse
try {
    $conn = Db::getConnection(); // Verbinding maken via Db-klasse

    // Haal de currency_unit op voor de ingelogde gebruiker
    $stmt = $conn->prepare("SELECT id, currency_unit FROM inlog WHERE email = :email");
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userId = $user['id']; // Gebruiker ID ophalen
        $currentCurrency = $user['currency_unit']; // Valuta van de gebruiker ophalen

        // Haal het subtotaal van het winkelmandje
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $total = 0;

        foreach ($cart as $product_id => $quantity) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id = :id");
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $total += $product['price'] * $quantity;
            }
        }

        // Controleer of de gebruiker genoeg currency heeft om de betaling te doen
        if ($currentCurrency >= $total) {
            // Begin een transactie
            $conn->beginTransaction();

            // **1. Bestelling opslaan in de orders tabel**
            $stmt = $conn->prepare("
                INSERT INTO orders (user_id, total_price, status, order_date, shipping_address, payment_method)
                VALUES (:user_id, :total_price, 'completed', NOW(), 'Geen adres', 'paypal')
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':total_price', $total);
            $stmt->execute();
            $orderId = $conn->lastInsertId(); // Het ID van de nieuwe bestelling ophalen

            // **2. Producten opslaan in de order_items tabel**
            foreach ($cart as $product_id => $quantity) {
                $stmt = $conn->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (:order_id, :product_id, :quantity, :price)
                ");
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $quantity);

                // Haal de prijs van het product op
                $stmtProduct = $conn->prepare("SELECT price FROM products WHERE id = :id");
                $stmtProduct->bindParam(':id', $product_id);
                $stmtProduct->execute();
                $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

                $stmt->bindParam(':price', $product['price']);
                $stmt->execute();
            }

            // **3. Update het currency_unit van de gebruiker**
            $newCurrency = $currentCurrency - $total;
            $stmt = $conn->prepare("UPDATE inlog SET currency_unit = :currency_unit WHERE email = :email");
            $stmt->bindParam(':currency_unit', $newCurrency);
            $stmt->bindParam(':email', $userEmail);
            $stmt->execute();

            // **4. Winkelwagen legen**
            unset($_SESSION['cart']);

            // Commit de transactie
            $conn->commit();

            // Redirect naar bevestigingspagina
            header('Location: bevestiging.php');
            exit();
        } else {
            // Foutmelding als er niet genoeg currency is
            echo "Je hebt niet genoeg currency om deze bestelling te betalen.";
        }

    } else {
        echo "Gebruiker niet gevonden.";
    }

} catch (PDOException $e) {
    // Rollback bij fout
    $conn->rollBack();
    echo "Fout bij verbinden met de database: " . $e->getMessage();
}
?>
