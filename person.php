<?php
require_once 'User.php';// Laad de Db-klasse
require_once 'session.php';


// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


try {
    // Maak verbinding via de Db-klasse
    $conn = Db::getConnection(); // Verbinding maken via Db-klasse

    // Haal de ingelogde user_id op uit de sessie
    $userId = $_SESSION['user_id'];

    // Haal persoonlijke gegevens op
    $stmt = $conn->prepare("SELECT email FROM inlog WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Gebruiker niet gevonden.";
        exit();
    }

    // Haal de bestellingen van de gebruiker op, inclusief producttitels
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(p.title SEPARATOR ', ') AS product_titles 
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = :user_id 
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Haal de resultaten op
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="person.css">
  <title>Mijn Account</title>
</head>
<body>
  <div class="container">
    <h1>Mijn Account</h1>

    <!-- Gebruikersinformatie -->
    <div class="section">
      <h2>Persoonlijke Gegevens</h2>
      <p><strong>Naam:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
      <p><strong>E-mailadres:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Wachtwoord wijzigen -->
    <div class="section">
      <h2>Wachtwoord Wijzigen</h2>
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current-password'], $_POST['new-password'])) {
          require_once 'User.php'; // Zorg ervoor dat de User-klasse beschikbaar is
          $userObj = new User($user['email']);
          $result = $userObj->changePassword($_POST['current-password'], $_POST['new-password']);
          echo "<p>" . htmlspecialchars($result) . "</p>";
      }
      ?>
      <form method="POST">
        <div class="form-group">
          <label for="current-password">Huidig Wachtwoord</label>
          <input type="password" id="current-password" name="current-password" required>
        </div>
        <div class="form-group">
          <label for="new-password">Nieuw Wachtwoord</label>
          <input type="password" id="new-password" name="new-password" required>
        </div>
        <button type="submit">Opslaan</button>
      </form>
    </div>

    <!-- Bestellingen -->
    <div class="section">
      <h2>Mijn Bestellingen</h2>

      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
          <div class="order-item">
            <p><strong>Bestelnummer:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Datum:</strong> <?php echo date('d-m-Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Bedrag:</strong> €<?php echo number_format($order['total_price'], 2); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            <p><strong>Betaalmethode:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
          </div>
          <hr>
        <?php endforeach; ?>
      <?php else: ?>
        <p>U heeft geen bestellingen.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>