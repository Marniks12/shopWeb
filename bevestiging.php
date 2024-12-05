<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bevestiging Betaling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .confirmation-message {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            font-size: 18px;
        }
        .back-button {
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="confirmation-message">
        <h2>Betaling Geslaagd!</h2>
        <p>Uw betaling is succesvol verwerkt. Uw nieuwe valuta-saldo is bijgewerkt.</p>
    </div>

    <a href="index.php" class="back-button">Terug naar de winkel</a>

</body>
</html>
