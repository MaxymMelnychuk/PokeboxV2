<?php

require_once("../../config/database.php");



function username($username) {
    if (strlen($username) >= 4) {
        // echo '<p style="color: green;">Username good</p>';
        return true;

    } else {
        echo '<p style="color: red;">Username doit contenir au moins 4 caractere</p>';
        return false;
    }
}


function logIn($password, $passwordRepeat) {
    
    if (strlen($password) > 8 && preg_match('/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;,.<>?]/', $password)) {
        if ($password == $passwordRepeat) {
            // echo '<p style="color: green;">Mot de passe correct</p>';
            return true;
        }
        else {
            echo '<p style="color: red;">Les mot de passes ne rassemblent pas</p>';
            return false;
        }
            
    } else {
        echo '<p style="color: red;">Mot de passe doit contenir au moins 8 caracteres, 1 minuscule, majuscule, et caractere special</p>';
        return false;
        
    }

   
}

function accepting($username, $password, $passwordRepeat, $pdo) {
    
    $isUsernameValid = username($username);        
    $isPasswordValid = logIn($password, $passwordRepeat); 
    
    if ($isUsernameValid && $isPasswordValid) {
        echo '<p style="color: green;">Formulaire envoyée</p>';

        if($_POST){

            $email = $_POST["email"];
            $password = $_POST["password"];
            $username = $_POST["username"];
        
            $sql = "INSERT INTO user (email, password, username) VALUES(:email, :password, :username)";
        
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'username' => $username
            ]);
        
            // echo "Votre user a été cocrrectement inséré en BDD";
        
        }
        
        
        
        
        
       
    } else {
        // echo '<p style="color: red;">NOOOO</p>';
        return false;
    }
}






if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Récupère et convertit les valeurs du formulaire
    $username = $_POST["username"];
    $password = $_POST['password'];
    $passwordRepeat = $_POST['passwordRepeat'];
   
 
    $accept = accepting($username, $password, $passwordRepeat, $pdo);
    
   
   
    
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokébox - Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <a href="../landing/index.html" class="back"> <- Back</a>
    <h1>Inscription</h1>
    <form method="POST" action="">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <label for="passwordRepeat">Confirmer le mot de passe :</label>
        <input type="password" id="passwordRepeat" name="passwordRepeat" required>

        <button type="submit">S'inscrire</button>
        <div class="register_form">
            <p>Déjà un compte ?</p>
            <a href="login.php">Se connecter</a>
        </div>
    </form>
</body>
</html>