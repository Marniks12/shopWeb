<?php
require_once 'session.php';


// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

// Databaseverbinding
$host = 'localhost';
$dbname = 'webshop1';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Haal de laatste bestelling op voor de ingelogde gebruiker
    $stmt = $conn->prepare("
        SELECT oi.product_id, p.title 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = :user_id
        ORDER BY o.order_date DESC
        LIMIT 5
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $orderedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
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