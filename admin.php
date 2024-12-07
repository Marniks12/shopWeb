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

require_once 'User.php';
 // Zorg ervoor dat de sessie wordt gestart

// Controleer of de gebruiker een admin is
$isAdmin = isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin'; 

// Stel user object in (of je kunt usertype ophalen)
$user = isset($_SESSION['email']) ? $_SESSION['email'] : null;

if ($isAdmin && $user): ?>
    <section class="admin-actions">


        <!-- Product toevoegen -->
        <section class="add-product">
            <h3>Nieuw product toevoegen</h3>
            <form method="POST" action="index.php" class="product-form">
                <input type="text" name="title" placeholder="Productnaam" required>
                <input type="number" name="price" step="0.01" placeholder="Prijs (€)" required>
                <input type="url" name="img" placeholder="Afbeeldings-URL" required>
                <input type="text" name="categorie_id" placeholder="Categorie-ID">
                <input type="text" name="description" placeholder="Beschrijving">
                <button type="submit" name="add_product" class="btn">Product Toevoegen</button>
            </form>
        </section>

        <!-- Product bijwerken en verwijderen -->
        <?php foreach ($products as $product): ?>
            <section class="update-product">
                <h3>Product: <?php echo htmlspecialchars($product['title']); ?></h3>

                <!-- Verwijder product -->
                <form method="POST" action="index.php" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">
                    <input type="hidden" name="delete_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn delete">Verwijderen</button>
                </form>

                <!-- Bijwerken van product -->
                <form method="POST" action="index.php" class="update-form">
                    <input type="hidden" name="update_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                    <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($product['img']); ?>" required>
                    <input type="text" name="categorie_id" value="<?php echo htmlspecialchars($product['categorie_id'] ?? ''); ?>">
                    <input type="text" name="description" value="<?php echo htmlspecialchars($product['description'] ?? ''); ?>">
                    <button type="submit" class="btn">Bijwerken</button>
                </form>
            </section>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
