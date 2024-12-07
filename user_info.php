<?php
require_once __DIR__ . '/session.php';


// Controleer of de gebruiker is ingelogd
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $username = $_SESSION['email'];  // Of gebruik $_SESSION['username'] als je dat hebt ingesteld
} else {
    $username = null;
}
?>
