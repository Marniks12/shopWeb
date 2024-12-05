<?php
session_start(); // Start de sessie

// Databaseverbinding
$host = 'localhost';
$dbname = 'webshop1';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Haal de email van de ingelogde gebruiker
    if (isset($_SESSION['email'])) {
        $userEmail = $_SESSION['email'];

        // Haal de currency_unit op voor de ingelogde gebruiker
        $stmt = $conn->prepare("SELECT currency_unit FROM inlog WHERE email = :email");
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $currency_unit = $user['currency_unit'];
        } else {
            // Als gebruiker niet gevonden is, stuur naar login
            header('Location: login.php');
            exit();
        }
    } else {
        // Als er geen ingelogde gebruiker is, stuur naar login
        header('Location: login.php');
        exit();
    }

    // Haal alle producten op uit de sessie
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $product_ids = array_keys($cart);

    if (!empty($product_ids)) {
        // Haal de productdetails op
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $products = [];
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Functie voor de Increase button
            document.querySelectorAll(".increase").forEach(button => {
                button.addEventListener("click", function () {
                    const input = this.previousElementSibling;
                    input.value = parseInt(input.value) + 1;
                    updateTotalAndSubtotal(input.dataset.productId, input.value);
                });
            });

            // Functie voor de Decrease button
            document.querySelectorAll(".decrease").forEach(button => {
                button.addEventListener("click", function () {
                    const input = this.nextElementSibling;
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                        updateTotalAndSubtotal(input.dataset.productId, input.value);
                    }
                });
            });

            // Functie om de hoeveelheden in de sessie en totaalprijs bij te werken
            function updateTotalAndSubtotal(productId, quantity) {
                fetch("update_cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update de totaalprijs per product
                        document.querySelector(`#total-${productId}`).innerText = `${data.new_total.toFixed(2)}`;
                        // Update de algemene subtotaal
                        updateSubtotal(data.subtotal);
                    }
                })
                .catch(error => console.error("Error updating cart:", error));
            }

            // Functie om het subtotaal van de winkelwagen bij te werken
            function updateSubtotal(subtotal) {
                document.querySelector("#subtotal").innerText = `${subtotal.toFixed(2)}`;
            }
        });
    </script>
</head>
<body>
    <h1>Uw Winkelwagen</h1>

    <!-- Toon de currency unit bovenaan -->
    <p>Uw huidige saldo: €<?php echo htmlspecialchars($currency_unit); ?></p>

    <?php if (!empty($products)): ?>
        <div class="cart-container">
            <?php foreach ($products as $product): ?>
                <div class="cart-item">
                    <div class="product-details">
                        <p><strong>Product:</strong> <?php echo htmlspecialchars($product['title']); ?></p>
                        <p><strong>Prijs:</strong> €<?php echo htmlspecialchars($product['price']); ?></p>
                    </div>
                    <div class="quantity">
                        <button class="decrease">−</button>
                        <input 
                            type="number" 
                            value="<?php echo htmlspecialchars($cart[$product['id']]); ?>" 
                            min="1" 
                            data-product-id="<?php echo $product['id']; ?>" 
                            readonly
                        >
                        <button class="increase">+</button>
                    </div>
                    <div class="total" id="total-<?php echo $product['id']; ?>">
                        <strong>Totaal:</strong> €<?php echo number_format($product['price'] * $cart[$product['id']], 2); ?>
                    </div>
                    <form method="POST" action="removecart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit">Verwijder</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="subtotal">
            <strong>Subtotaal:</strong> <span id="subtotal"><?php echo number_format(array_reduce($products, function ($total, $product) use ($cart) {
                return $total + $product['price'] * $cart[$product['id']];
            }, 0), 2); ?></span>
        </div>

        <!-- Betalen knop -->
        <form method="POST" action="betalen.php">
            <button type="submit" name="pay" id="pay-button">Betalen</button>
        </form>
    <?php else: ?>
        <p>Uw winkelwagen is leeg.</p>
    <?php endif; ?>

    <a href="index.php">Verder winkelen</a>
</body>
</html>

