<?php
require_once 'session.php';

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
        $statement = $this->db->prepare('SELECT password FROM inlog WHERE email = :email');
        $statement->bindValue(':email', $this->email);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($currentPassword, $user['password'])) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);

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
            // Start de sessie (verplaatst naar header.php)
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['usertype'] = $user['usertype'];
            $_SESSION['user_id'] = $user['id'];

            return true;
        }
        return false;
    }

    // Functie om te controleren of de gebruiker een admin is
    public static function isAdmin() {
        return isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin';
    }

    // Functie om het type van de ingelogde gebruiker op te halen
    public static function getUserType() {
        return isset($_SESSION['usertype']) ? $_SESSION['usertype'] : null;
    }

    // Functie om de gebruiker uit te loggen
    public static function logout() {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Controleer of de gebruiker is ingelogd
    public static function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
    }

    // Functie om product bij te werken (alleen voor admins)
    public function updateProduct($productId, $title, $price, $img, $categorie_id = null, $description = null) {
        if (self::isAdmin()) {
            if (!empty($title) && !empty($price) && !empty($img)) {
                try {
                    $stmt = $this->db->prepare(
                        "UPDATE products 
                         SET title = :title, price = :price, img = :img, categorie_id = :categorie_id, description = :description 
                         WHERE id = :id"
                    );
                    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
                    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
                    $stmt->bindParam(':img', $img, PDO::PARAM_STR);
                    $stmt->bindParam(':categorie_id', $categorie_id, PDO::PARAM_STR);
                    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                    $stmt->execute();
                    return "Product succesvol bijgewerkt!";
                } catch (PDOException $e) {
                    return "Fout bij het bijwerken van product: " . $e->getMessage();
                }
            } else {
                return "Alle velden zijn verplicht.";
            }
        } else {
            return "Geen rechten om product te bewerken.";
        }
    }
    
    
    public function addProduct($title, $price, $img, $categorie_id = null, $description = null) {
        if (self::isAdmin()) {
            if (!empty($title) && !empty($price) && !empty($img)) {
                try {
                    $stmt = $this->db->prepare(
                        "INSERT INTO products (title, price, img, categorie_id, description) 
                         VALUES (:title, :price, :img, :categorie_id, :description)"
                    );
                    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
                    $stmt->bindParam(':img', $img, PDO::PARAM_STR);
                    $stmt->bindParam(':categorie_id', $categorie_id, PDO::PARAM_STR);
                    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                    $stmt->execute();
                    return "Product succesvol toegevoegd!";
                } catch (PDOException $e) {
                    return "Fout bij het toevoegen van product: " . $e->getMessage();
                }
            } else {
                return "Alle velden zijn verplicht.";
            }
        } else {
            return "Geen rechten om product toe te voegen.";
        }
    }

    public function deleteProduct($productId) {
        if (self::isAdmin()) {
            try {
                $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
                $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                return "Product succesvol verwijderd!";
            } catch (PDOException $e) {
                return "Fout bij het verwijderen van product: " . $e->getMessage();
            }
        } else {
            return "Geen rechten om product te verwijderen.";
        }
    }
}
?>
