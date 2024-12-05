<?php
require 'User.php';
//if ($user && password_verify($password, $user['password'])) {
    // Start sessie en stel sessiegegevens in
    //session_start();
   // $_SESSION['loggedin'] = true;
   // $_SESSION['email'] = $email;
   // $_SESSION['usertype'] = $user['usertype']; // Voeg usertype toe
    //return true;
//}

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (User::login($email, $password)) {
        header('Location: home.php');
    } else {
        $error = "Ongeldig e-mailadres of wachtwoord.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Log in</h1>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>E-mailadres:</label>
        <input type="email" name="email" required>
        <br>
        <label>Wachtwoord:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Log in">
    </form>


	<!-- Knop om naar "Wachtwoord wijzigen" te gaan -->
    <a href="change_password.php">
    <button type="button" style="margin-top: 20px;">Wachtwoord wijzigen</button>
</a>

</body>
</html>
