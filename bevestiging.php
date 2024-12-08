<?php
require_once 'session.php';
require_once 'User.php';
 // Zorg ervoor dat de Db-klasse is ingeladen



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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bevestiging Betaling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .confirmation-message {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .back-button {
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
        }
        .comment-section {
            margin-top: 30px;
            text-align: left;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .comment-section h3 {
            margin-bottom: 10px;
        }
        .comment-section textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .comment-section button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .comment-section button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="confirmation-message">
        <h2>Betaling Geslaagd!</h2>
        <p>Uw betaling is succesvol verwerkt. Uw nieuwe valuta-saldo is bijgewerkt.</p>
    </div>

    <div class="comment-section">
        <h3>Laat een commentaar achter voor elk product:</h3>
        <?php if (!empty($orderedProducts)): ?>
            <?php foreach ($orderedProducts as $product): ?>
                <form class="comment-form" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <label for="comment-<?php echo htmlspecialchars($product['product_id']); ?>">
                        <?php echo htmlspecialchars($product['title']); ?>
                    </label>
                    <textarea id="comment-<?php echo htmlspecialchars($product['product_id']); ?>" rows="4" placeholder="Schrijf een commentaar..." required></textarea>
                    <button type="button" onclick="submitComment(<?php echo htmlspecialchars($product['product_id']); ?>)">Indienen</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Er zijn geen producten om te beoordelen.</p>
        <?php endif; ?>
    </div>

    <a href="index.php" class="back-button">Terug naar de winkel</a>

    
              
    <script>
    async function submitComment(productId) {
        const textarea = document.getElementById(comment-${productId});
        const comment = textarea.value;

        // Als het commentaarveld leeg is, voer dan niets uit (geen waarschuwing)
        if (comment.trim() === '') {
            return; // Doe verder niets als het commentaarveld leeg is
        }

        try {
            const response = await fetch('add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    comment: comment
                })
            });

            if (response.ok) {
                // Leeg het commentaarveld na succes
                textarea.value = ''; // Clear textarea

                // Optioneel: Voer hier een succesbericht uit, bijvoorbeeld:
                const feedbackContainer = document.getElementById('feedback-container');
                feedbackContainer.innerHTML = '<p class="success-message">Commentaar succesvol toegevoegd!</p>';
            } else {
                // Foutmelding bij mislukte poging om commentaar toe te voegen
                const feedbackContainer = document.getElementById('feedback-container');
                feedbackContainer.innerHTML = '<p class="error-message">Er is een fout opgetreden bij het toevoegen van het commentaar.</p>';
            }
        } catch (error) {
            console.error('Netwerkfout:', error);
            const feedbackContainer = document.getElementById('feedback-container');
            feedbackContainer.innerHTML = '<p class="error-message">Netwerkfout: Probeer het later opnieuw.</p>';
        }
    }
</script>


</body>
</html>