<?php
require_once("connexion.php");

///// LOGIN.PHP

if(isset($_SESSION["iduser"])) {
    header("location:dashboard.php");
}

if ($_POST) {

    $email = $_POST["email"];
    $password = trim($_POST["password"]);

    if ($email && $password) {

        // si j'ai un post
        // je récupère email et password
        // je récupère les infos du user en bdd pour cet email
        // SELCT ... WHERE email =...
        // je variabilise avec un fetch
        $stmt = $pdo->query("SELECT * FROM user WHERE email = '$email' ");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // je vérifie si le mot de passer de mon form et celui en bdd sont les même
        // password_verify
        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["iduser"] = $user["iduser"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["username"] = $user["username"];
            header("location:dashboard.php");
        } else {
            echo "La connexion a échoué !";
        }

        // si c'est le cas
        // j'alimente ma session avec l'id, l'email en sesssion

        // message de confirmation : vous êtes connecté avec l'identifiant : email@mail.com


    }

}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>

<body>


    <h1>Login</h1>
   
    <?php if (!isset($_SESSION["iduser"])) { ?>
        <form  method="POST">

        <label for="email">Email :</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password :</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
        <div class="register_form">
            <p>Don't have an account ?</p>
            <a href="register.php">Sign up</a>
            
        </div>

        </form>

    <?php } ?>

</body>

</html>
