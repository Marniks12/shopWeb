<?php
require_once 'session.php';
require_once 'User.php'; 
include_once 'user_info.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    header('Location: cart.php');
    exit;
}

try {
    $conn = Db::getConnection();

    if (!isset($_GET['id'])) {
        exit("Product niet gevonden.");
    }

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit("Product niet gevonden.");
    }

    $commentStmt = $conn->prepare("
        SELECT c.comment, u.email, c.created_at
        FROM comments c
        JOIN inlog u ON c.user_id = u.id
        WHERE c.product_id = :product_id
        ORDER BY c.created_at DESC
    ");
    $commentStmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_INT);
    $commentStmt->execute();
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Databaseverbinding mislukt: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="detail.css">
    <title><?php echo htmlspecialchars($product['title']); ?></title>
</head>
<body>
    <?php include 'header.php'; ?>

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

    <div id="comments">
        <h3>Reacties:</h3>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['email']); ?></strong> schreef:</p>
                    <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    <p><small>Op <?php echo htmlspecialchars($comment['created_at']); ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Er zijn nog geen reacties voor dit product.</p>
        <?php endif; ?>
    </div>

    <div id="comment-form">
        <h3>Laat een reactie achter:</h3>
        <textarea id="comment-text" rows="4" placeholder="Schrijf hier je commentaar..." required></textarea>
        <button type="button" onclick="submitComment()">Indienen</button>
    </div>

    <script>
    async function submitComment() {
        const commentText = document.getElementById('comment-text').value;
        const productId = <?php echo $_GET['id']; ?>;

        if (!commentText.trim()) {
            alert('Commentaar mag niet leeg zijn.');
            return;
        }

        try {
            const response = await fetch('add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    comment: commentText
                })
            });

            const result = await response.json();
            if (result.success) {
                const commentSection = document.getElementById('comments');
                const newComment = document.createElement('div');
                newComment.classList.add('comment');
                newComment.innerHTML = `<p><strong>Jij</strong> schreef:</p><p>${commentText}</p><p><small>Op nu</small></p>`;
                commentSection.insertBefore(newComment, commentSection.firstChild);

                document.getElementById('comment-text').value = '';
            } else {
                alert('Er is een fout opgetreden bij het toevoegen van je commentaar.');
            }
        } catch (error) {
            console.error('Fout bij het verzenden van het commentaar:', error);
            alert('Er is een netwerkfout opgetreden.');
        }
    }
    </script>
</body>
</html>
