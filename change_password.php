<?php
require_once 'User.php'; // Zorg ervoor dat je de User-klasse laadt

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Maak een nieuwe User-klasse aan
        $user = new User();

        // Registreer de admin
        $result = $user->registerAdmin($email, $password);

        // Toon een succesbericht en redirect naar admin.php
        echo $result;
        header('Location: admin.php');
        exit;
    } catch (Exception $e) {
        // Toon een foutmelding als er iets misgaat
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreer Admin</title>
</head>
<body>
    <h1>Registreer als Admin</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="email">E-mailadres:</label>
        <input type="email" name="email" id="email" required>
        <br>
        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Registreer</button>
    </form>
</body>
</html>
