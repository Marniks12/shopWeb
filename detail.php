?php
require_once 'session.php';

include_once 'user_info.php'; // Laadt het bestand waar de gebruikersnaam wordt opgehaald

 // Start de sessie

// Voeg het product toe aan de winkelwagen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Controleer of het product al in de winkelwagen zit
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity; // Voeg toe aan bestaande hoeveelheid
    } else {
        $_SESSION['cart'][$product_id] = $quantity; // Voeg nieuw product toe
    }

    // Redirect terug naar de productpagina
    header('Location: cart.php');
    exit;
}

// Database connection details
$host = 'localhost'; // Your MySQL server
$dbname = 'webshop1'; // Your database name
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL password

try {
    // Maak verbinding met de database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Zet PDO foutmodi op uitzondering
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Haal producten op uit de database
    $stmt = $conn->prepare("SELECT title, price, img, id FROM products");
    $stmt->execute();

    // Fetch alle producten als een associatieve array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Controleer of een ID is doorgegeven
if (!isset($_GET['id'])) {
    exit("Product not found.");
}

// Haal productdetails en commentaren op uit de database
try {
    // Haal het product op op basis van de ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit("Product not found.");
    }

   // Haal commentaren op voor dit product
$commentStmt = $conn->prepare("
SELECT c.comment, u.email, c.created_at
FROM comments c
JOIN inlog u ON c.user_id = u.id  -- Gebruik hier 'user_id' in plaats van 'inlog_id'
WHERE c.product_id = :product_id
ORDER BY c.created_at DESC
");
    $commentStmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_INT);
    $commentStmt->execute();
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="detail.css">
    <title><?php echo htmlspecialchars($product['title']); ?></title>
    <script>
        // Haal de comments op via AJAX
        function loadComments(productId) {
            fetch(get_comments.php?id=${productId})
                .then(response => response.json())
                .then(data => {
                    const commentSection = document.getElementById('comments');
                    commentSection.innerHTML = ''; // Maak de sectie leeg

                    if (data.error) {
                        commentSection.innerHTML = <p>Error: ${data.error}</p>;
                    } else if (data.length === 0) {
                        commentSection.innerHTML = '<p>Er zijn nog geen reacties voor dit product.</p>';
                    } else {
                        data.forEach(comment => {
                            const commentDiv = document.createElement('div');
                            commentDiv.className = 'comment';
                            commentDiv.innerHTML = 
                                <strong>${comment.username}</strong>
                                <p>${comment.comment}</p>
                            ;
                            commentSection.appendChild(commentDiv);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching comments:', error);
                    document.getElementById('comments').innerHTML = '<p>Fout bij het laden van reacties.</p>';
                });
        }

        // Roep de functie aan zodra de pagina geladen is
        document.addEventListener('DOMContentLoaded', function () {
            const productId = <?php echo json_encode($_GET['id']); ?>; // Verkrijg het product ID
            loadComments(productId);
        });</script>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Header wordt hier ingeladen -->

    <div class="product-container">
        <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product Image" class="product-image">
    </div>

    <div class="product-info">
        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
        <p class="product-description">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum.
        </p>
        <p class="product-price">€ <?php echo htmlspecialchars($product['price']); ?></p>
        <a href="index.php" class="back-button">Back to Products</a>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit">Voeg toe aan winkelmandje</button>
        </form>
    </div>

    <!-- Commentaar sectie -->
    <div id="comments">
        <!-- Reacties worden hier geladen -->
    </div>
</body>
</html>