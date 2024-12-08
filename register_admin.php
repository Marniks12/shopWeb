<?php
require_once 'session.php';
require_once 'User.php'; // Laad de Db-klasse

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Maak verbinding via de Db-klasse
        $conn = Db::getConnection(); // Verbinding maken via Db-klasse

        // Gegevens ophalen uit het formulier
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Versleutel het wachtwoord
        $usertype = 'admin'; // Zet de usertype als 'admin'

        // Voeg de admin toe aan de database
        $stmt = $conn->prepare("INSERT INTO inlog (email, password, usertype) VALUES (:email, :password, :usertype)");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':usertype', $usertype);

        if ($stmt->execute()) {
            echo "Admin succesvol geregistreerd!";
            header('Location: admin.php'); // Stuur door naar de adminpagina na registratie
            exit;
        } else {
            echo "Er is een fout opgetreden bij de registratie.";
        }
    } catch (PDOException $e) {
        echo "Databasefout: " . $e->getMessage();
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
