<?php 
require 'vendor/autoload.php';

// Utilise la librairie Guzzle pour les requêtes HTTP
use GuzzleHttp\Client;

// Initialisation du client HTTP
$client = new Client();

$ok = "ok";
function logIn($password) {
    
    if (strlen($password) < 5) {
        echo '<p style="color: red;">Mot de passe incorrect</p>';
    } else {
        echo "<p>Mot de passe correct</p>";
    }

    return $password;
}




if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Récupère et convertit les valeurs du formulaire

    $password = $_POST['password'];
   
    

    // Calcule la moyenne en appelant la fonction
    $verify= logIn($password);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <label for="username">Username :</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password :</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
        <div class="register_form">
            <p>Don't have account ?</p>
            <a href="register.php">Register</a>
            <p><?php echo $password; ?></p>
        </div>
        
    </form>

</body>
</html>