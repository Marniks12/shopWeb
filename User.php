<?php
class User {
    private $db;
    private $email;

    // Constructor: verbind met de database en stel e-mailadres in
    public function __construct($email) {
        $this->db = new PDO('mysql:host=localhost;dbname=webshop1', 'root', '');
        $this->email = $email;
    }


    
    // Functie om het wachtwoord te wijzigen
    public function changePassword($currentPassword, $newPassword) {
        // Haal het huidige wachtwoord op uit de database
        $statement = $this->db->prepare('SELECT password FROM inlog WHERE email = :email');
        $statement->bindValue(':email', $this->email);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($currentPassword, $user['password'])) {
            // Hash het nieuwe wachtwoord
            $options = ['cost' => 12];
            $hash = password_hash($newPassword, PASSWORD_DEFAULT, $options);

            // Update het wachtwoord in de database
            $updateStatement = $this->db->prepare('UPDATE inlog SET password = :password WHERE email = :email');
            $updateStatement->bindValue(':password', $hash);
            $updateStatement->bindValue(':email', $this->email);
            $updateStatement->execute();

            return "Wachtwoord succesvol gewijzigd.";
        } else {
            return "Ongeldig huidig wachtwoord.";
        }
    }

    // Functie om het ingelogde e-mailadres op te halen
    public function getEmail() {
        return $this->email;
    }

    // Functie om een gebruiker in te loggen
    public static function login($email, $password) {
        $db = new PDO('mysql:host=localhost;dbname=webshop1', 'root', '');
        $statement = $db->prepare('SELECT * FROM inlog WHERE email = :email');
        $statement->bindValue(':email', $email);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Start sessie en stel sessiegegevens in
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['usertype'] = $user['usertype'];
            // Na succesvolle login
            $_SESSION['user_id'] = $user['id'];  // Sla de user_id op in de sessie

            return true;
        }
        return false;
    }

    // Functie om de gebruiker uit te loggen
    public static function logout() {
        session_start();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Controleer of de gebruiker is ingelogd
    public static function isLoggedIn() {
        session_start();
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
    }
}
?>
