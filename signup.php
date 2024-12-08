
<?php
require_once 'session.php';

require 'User.php';

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verbind met de database
   require_once 'db.php';

    // Controleer of het e-mailadres al bestaat
    $statement = $conn->prepare('SELECT * FROM inlog WHERE email = :email');
    $statement->bindValue(':email', $email);
    $statement->execute();
    $existingUser = $statement->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = "Dit e-mailadres is al geregistreerd. Probeer een ander e-mailadres.";
    } else {
        // Hash het wachtwoord
        $options = ['cost' => 12];
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        // Voeg de gebruiker toe aan de database
        $insertStatement = $conn->prepare('INSERT INTO inlog (email, password, usertype, currency_unit) VALUES (:email, :password, "user", 1000)');
        $insertStatement->bindValue(':email', $email);
        $insertStatement->bindValue(':password', $hash);
        $insertStatement->execute();

        $success = "Account succesvol aangemaakt! Je kunt nu inloggen.";
        header('Location: login.php'); // Zorg ervoor dat dit naar je loginpagina verwijst
        exit(); // Beëindig script om verder uitvoeren te voorkomen
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Pagina</title>
    <link rel="stylesheet" href="signup.css"> <!-- Zorg ervoor dat dit naar je CSS-bestand verwijst -->
</head>
<body>

    <!-- Signup Container -->
    <div class="signup-container">
        <!-- Titel -->
        <h1 class="title">Maak een nieuw account</h1>

        <!-- Feedbackberichten -->
        <?php if (isset($error)): ?>
            <div class="error">
                <p><?php echo $error; ?></p>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="success">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <!-- Signup Formulier -->
        <form class="form--signup" action="" method="post">
            <!-- E-mail veld -->
            <div class="email">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Wachtwoord veld -->
            <div class="password">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Signup knop -->
            <div class="click">
                <input type="submit" value="Registreren">
            </div>
        </form>
    </div>

</body>
</html>
