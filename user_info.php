<?php
require_once __DIR__ . '/session.php';
require_once 'User.php';  // Laad de Db-klasse voor de databaseverbinding

// Controleer of de gebruiker is ingelogd
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $username = $_SESSION['email'];  // Of gebruik $_SESSION['username'] als je dat hebt ingesteld
} else {
    $username = null;
}

// Als je gegevens van de ingelogde gebruiker nodig hebt (bijv. voor profielinformatie):
try {
    // Verkrijg de databaseverbinding via de Db-klasse
    $conn = Db::getConnection();

    if ($username) {
        // Verkrijg gegevens van de ingelogde gebruiker uit de database
        $stmt = $conn->prepare("SELECT id, username, email FROM inlog WHERE email = :email");
        $stmt->bindParam(':email', $username, PDO::PARAM_STR);
        $stmt->execute();

        // Verkrijg de gebruiker
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Als je bijvoorbeeld de gebruikersnaam nodig hebt
        if ($user) {
            $userId = $user['id'];
            $email = $user['email'];
            $username = $user['username']; // Dit kan variëren afhankelijk van de kolomnaam
        } else {
            // Gebruiker niet gevonden
            $username = null;
        }
    } else {
        // Gebruiker is niet ingelogd
        $username = null;
    }

} catch (PDOException $e) {
    echo "Fout bij het ophalen van gegevens: " . $e->getMessage();
}
?>

