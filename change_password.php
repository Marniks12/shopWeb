<<<<<<< HEAD
=======
<?php
require_once 'session.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

require 'User.php';

// Maak een User-object voor de ingelogde gebruiker
$email = $_SESSION['email'];
$user = new User($email);

if (!empty($_POST)) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Wijzig het wachtwoord via de User-class
    $message = $user->changePassword($currentPassword, $newPassword);

    if ($message === "Wachtwoord succesvol gewijzigd.") {
        // Vernietig de sessie (indien nodig) en stuur de gebruiker naar de loginpagina
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wachtwoord wijzigen</title>
</head>
<body>
    <h1>Wachtwoord wijzigen</h1>
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Huidig wachtwoord:</label>
        <input type="password" name="current_password" required>
        <br>
        <label>Nieuw wachtwoord:</label>
        <input type="password" name="new_password" required>
        <br>
        <input type="submit" value="Wijzig wachtwoord">
    </form>
</body>
</html>
ChatGPT zei:
ChatGPT
Om dezelfde wijziging aan te brengen waarbij je de juiste require_once paden toevoegt met behulp van __DIR__, kun je de volgende aanpassingen maken. Dit zorgt ervoor dat de bestanden altijd correct worden ingeladen, ongeacht de locatie van de scriptuitvoering.

Aangepaste versie van de PHP-code met __DIR__ voor correcte paden:
php
Code kopiëren
>>>>>>> a81c8c76066edf1cd2dc8628844ed964529a7d50
<?php
require_once 'session.php';
require 'User.php'; // Zorg ervoor dat de User-klasse geladen is
// Zorg ervoor dat de Db-klasse geladen is

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}


// Maak een User-object voor de ingelogde gebruiker
$email = $_SESSION['email'];
$user = new User($email);

// Verwerk de wijziging van het wachtwoord als er een POST-verzoek is
if (!empty($_POST)) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Wijzig het wachtwoord via de User-class
    $message = $user->changePassword($currentPassword, $newPassword);

    if ($message === "Wachtwoord succesvol gewijzigd.") {
        // Vernietig de sessie (indien nodig) en stuur de gebruiker naar de loginpagina
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wachtwoord wijzigen</title>
</head>
<body>
    <h1>Wachtwoord wijzigen</h1>
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Huidig wachtwoord:</label>
        <input type="password" name="current_password" required>
        <br>
        <label>Nieuw wachtwoord:</label>
        <input type="password" name="new_password" required>
        <br>
        <input type="submit" value="Wijzig wachtwoord">
    </form>
</body>
</html>
