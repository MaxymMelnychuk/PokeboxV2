<?php

require_once("connexion.php");



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
        
            echo "Votre user a été cocrrectement inséré en BDD";
        
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Login</title>
</head>
<body>

<h2>Sign up</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <div>
            <label for="username">Username :</label><br>
            <input type="text" id="username" name="username" required><br><br>
        </div>

        <div>
            <label for="password">Password :</label><br>
            <input type="password" id="password" name="password" required><br><br>
        </div>

        <div>
            <label for="passwordRepeat">Password repeat :</label><br>
            <input type="passwordRepeat" id="passwordRepeat" name="passwordRepeat" required><br><br>
        </div>

        <div>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" required><br><br>
        </div>

        <button onclick="accepting()" type="submit">Register</button>

        <div class="register_form">
            <p>Already have account ?</p>
            <a href="login.php">Login</a>
            <p><?php ; ?>
            <?php ; ?></p>
        </div>
        
    </form>


        
        
        <script src="register.js"></script>
</body>
</html>