<?php
require_once __DIR__ . '/session.php';  // Correcte pad naar session.php
require_once __DIR__ . '/db.php';       // Correcte pad naar db.php

class Product {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Valideer de productgegevens
     */
    private function validateProduct($title, $price, $img) {
        if (empty($title)) {
            throw new Exception("De titel mag niet leeg zijn.");
        }
        if ($price < 0) {
            throw new Exception("De prijs mag niet negatief zijn.");
        }
        if ($img['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Er is een probleem opgetreden bij het uploaden van de afbeelding.");
        }
    }

    /**
     * Upload de afbeelding
     */
    private function uploadImage($img) {
        $targetDir = "uploads/"; // Zorg dat deze map bestaat
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetFile = $targetDir . basename($img["name"]);
        if (!move_uploaded_file($img["tmp_name"], $targetFile)) {
            throw new Exception("Kon de afbeelding niet uploaden.");
        }

        return $targetFile;
    }

    /**
     * Voeg een product toe aan de database
     */
    public function addProduct($title, $price, $img) {
        // Validatie
        $this->validateProduct($title, $price, $img);

        // Afbeelding uploaden
        $imagePath = $this->uploadImage($img);

        // Voeg het product toe aan de database
        $stmt = $this->conn->prepare("INSERT INTO products (title, price, img) VALUES (:title, :price, :img)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':img', $imagePath);

        if (!$stmt->execute()) {
            throw new Exception("Kon het product niet toevoegen aan de database.");
        }
    }
}
?>
