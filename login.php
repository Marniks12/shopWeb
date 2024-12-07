<?php
require_once 'session.php';

require 'User.php';

// Start de sessie voor sessiebeheer


// Controleer of het formulier is ingediend
if (!empty($_POST)) {
    // Haal de ingevoerde e-mail en wachtwoord op uit het formulier
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Probeer in te loggen met de User::login methode
        if (User::login($email, $password)) {
            // Succesvolle login, redirect naar home.php
            header('Location: home.php');
            exit(); // Zorg ervoor dat het script stopt na de redirect
        } else {
            // Foutmelding voor ongeldige inloggegevens
            $error = "Ongeldig e-mailadres of wachtwoord.";
        }
    } catch (PDOException $e) {
        // Foutafhandeling voor databasefouten
        $error = "Er is een probleem met de database. Probeer het later opnieuw.";
        // Log de fout (optioneel)
        error_log('Database fout: ' . $e->getMessage());
    }
}
?>









<!DOCTYPE html>
<html lang="nl">
    <link rel="stylesheet" href="login.css">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
        <h1>Log in</h1>
        <label>E-mailadres:</label>
        <input type="email" name="email" required>
        <br>
        <label>Wachtwoord:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Log in">
        
            <!-- Knop om naar "Wachtwoord wijzigen" te gaan -->
            <a href="change_password.php">
            <button type="button" style="margin-top: 20px;">Wachtwoord wijzigen</button>
        </a>
        
        <a href="register_admin.php">
            <button type="button" style="margin-top: 20px;">registeer als admin</button>
        </a>
    </form>

</body>
</html>