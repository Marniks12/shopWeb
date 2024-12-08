
<?php
require 'session.php';
require 'User.php';

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Probeer de gebruiker te registreren via de User-klasse
    $result = User::signup($email, $password);

    if ($result === true) {
        $success = "Account succesvol aangemaakt! Je kunt nu inloggen.";
        header('Location: login.php'); // Redirect naar de loginpagina
        exit();
    } else {
        $error = $result; // Foutmelding als string teruggegeven
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
