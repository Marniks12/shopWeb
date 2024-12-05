<?php
require_once 'User.php';

// Controleer of de gebruiker is ingelogd en een admin is
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: login.php'); // Stuur niet-admins terug naar de loginpagina
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welkom, Admin!</h1>
    <p>Hier kun je producten beheren, toevoegen en verwijderen.</p>

    <!-- Voeg links toe om producten te beheren -->
    <a href="add_product.php">Producten toevoegen</a><br>
    <a href="delete_product.php">Producten verwijderen</a><br>
</body>
</html>
