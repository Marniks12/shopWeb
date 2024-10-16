<?php
function canLogin($p_email, $p_password){
	if($p_email ==="Marvis" && $p_password==="123"){
		return true;
	}
	else{
		return false;
	}
} 
//wanneer gfaan we pas inloggen
if(!empty($_POST)){
	$email = $_POST['email'];
	$password = $_POST['password'];

	//$result = canLogin($email,$password)
	if (canLogin($email,$password)){
		session_start();
		$_SESSION['loggedin'] = true;
		$_SESSION['email'] = $email;
		//$salt = "312E3REFGREFVDER424444";
		//OK
		//$cookieValue = $email . "," . md5 ($email.$salt);
		//echo $cookieValue;
	//exit();
		//setcookie("login", $cookieValue, time()+60*60*24*30);
		header('Location: index.php');
	}
	else{
		$error =true;
	}
 //echo $email; nu wordt de echo gebruikt als consolelog 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="webshop.css">
    <title>Document</title>
   
</head>
<body>
  
<h1 class= "title">
Log in
</h1>
<div class="netflixLogin">
		<div class="form form--login">
			<form action="" method="post">
				
				<?php if(isset($error) ):?>

				<div class="error">
					<p>
						Sorry, we can't log you in with that email address and password. Can you try again?
					</p>
				</div>

				<?php endif;?>
					
				<div class="email">
					<label for="Email">Email</label>
					<input type="text" name="email">
				</div>

				<div class="password">
					<label for="Password">Password</label>
					<input type="password" name="password">
				</div>

				<div class="click">
					<input type="submit" value="Sign in" class="btn btn--primary">	
					<input type="checkbox" id="rememberMe"><label for="rememberMe" class="label__inline">Remember me</label>
				</div>
			</form>
		</div>
	</div>
</body>
</html>