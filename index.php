<?php
$conn = new PDO('mysql:dbname=webshop1;host=localhost', 'root', '');
// select * from products and fetch as array
$statement= $conn->prepare("SELECT * FROM products");
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="webshop.css">
    <title>Document</title>
</head>
<body>
    <h1>hello</h1>
<?php foreach($products as $product): ?>
<article>
    <h2 class= "product" >
    <?php echo $product['title']  ?>: €<?php echo $product['price']?>
    </h2>
</article>
<?php endforeach ?>
    
</body>
</html>