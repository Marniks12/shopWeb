<?php
require_once 'session.php';

$host = 'localhost'; // Your MySQL server
$dbname = 'webshop1'; // Your database name
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL passwordends

try {
    // Create a PDO instance (connect to the database)
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch products from the database
    $stmt = $conn->prepare("SELECT title, img FROM categories");
    
    $stmt->execute();

    // Fetch all products as an associative array
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $conn->prepare("SELECT img FROM promo");
    $stmt->execute();
    $promos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Document</title>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Header wordt hier ingeladen -->
    
    
    <h1 class="heading">Onze categorieen</h1>
    <?php if (!empty($categories)): ?>
        <div class="categories-container"> <!-- Nieuwe container voor flexbox -->
            <?php foreach ($categories as $categorie): ?>
                <article>
                    <h2 class="categorie">
                        <?php echo htmlspecialchars($categorie['title']); ?>
                    </h2>
                    <img src="<?php echo htmlspecialchars($categorie['img']); ?>" alt="Product" />
                </article>
                <?php endforeach; ?>
            </div> <!-- Einde van de flex-container -->
            <?php else: ?>
                <p>No categories available at the moment.</p>
                <?php endif; ?>
                
                <div class="dunne-lijn"></div>
                <h1>De nieuwste producten</h1>
                <!-- Slideshow container -->
                <div class="slideshow-container">
                    <?php foreach ($promos as $promo): ?>
                        <div class="promo-slide">
                            <img class="promoimg" src="<?php echo htmlspecialchars($promo['img']); ?>" alt="Promo Image">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    
                    <!-- Knoppen voor navigatie -->
                    <button class="prev" onclick="changeSlide(-1)">❮</button>
                    <button class="next" onclick="changeSlide(1)">❯</button>
                    <h5 class="closing">
                        Welcome to Soundscape - Premium Audio for Every Moment
                        Soundscape is your go-to for high-quality audio gear, 
                        offering the latest in headphones, earbuds, speakers, 
                        and accessories. Designed for both comfort and performance, 
                        our products deliver powerful sound and cutting-edge features like Bluetooth® connectivity and noise cancellation. 
                        Whether you’re after deep bass, crisp highs, or all-day comfort, Soundscape has the perfect audio solution for you.
                    </h5>
                    
    <script src="script.js"></script>
</body>

</html>