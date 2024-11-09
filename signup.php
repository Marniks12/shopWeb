<?php
    if(!empty($_POST)){
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $options = ['cost' => 12];
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT,$options);
        

        $conn = new PDO('mysql:host=localhost;dbname=webshop1', 'root', '',);
        $statement = $conn->prepare('INSERT INTO inlog (email, password) values (:email, :password)');
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hash);
		$statement->execute();
        
    }
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Pagina</title>
    <link rel="stylesheet" href="signup.css"> <!-- Zorg ervoor dat dit naar je CSS-bestand verwijst -->
</head>
<body>

    <!-- Signup Container -->
    <div class="signup-container">
        <!-- Titel -->
        <h1 class="title">Maak een nieuw account</h1>

        <!-- Signup Formulier -->
        <form class="form--signup" action="" method="post">
          
            

            <!-- E-mail veld -->
            <div class="email">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Wachtwoord veld -->
            <div class="password">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" required>
            </div>

           

            <!-- Signup knop -->
            <div class="click">
                <input type="submit" value="Registreren">
            </div>
            <?php if(isset($error)): ?>
            <!-- Foutmelding (indien van toepassing) -->
            <div class="error" style="display:none;">
                <!-- Hier komt de foutmelding -->
                Oeps, iets ging fout. Probeer opnieuw.
            </div>
            <?php endif ?>
        </form>
    </div>

</body>
</html>
