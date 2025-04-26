<?php

///// PROFILE.PHP
require_once("connexion.php");


if(!isset($_SESSION["iduser"])) {
    header("location:login.php");
}

// Déconnexion
if (isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    session_unset();
    session_destroy();
    header("location:login.php");
    exit;
}



if(isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    // je vide ma session
    unset($_SESSION["iduser"]);
    unset($_SESSION["email"]);
    unset($_SESSION["username"]);
    header("location:login.php"); // redirection sans paramètre
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Responsive</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="wrapper" id="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <ul>
        <li><a href="#">Profil</a></li>
        <li><a href="#">Mes cartes</a></li>
        <li><a href="#">Autre</a></li>
        <li><a href="?action=deconnexion">Déconnexion</a></li>
      </ul>
    </nav>

    <!-- Contenu principal -->
    <main class="main-content">
      <button id="toggle-btn" class="toggle-btn">&#9776;</button>
      <?php

        echo "<h1> Salut,  " . $_SESSION["username"] . "</h1>";
    
    ?>
      <p></p>
    </main>
  </div>

  <script>
    const wrapper = document.getElementById('wrapper');
    const toggleBtn = document.getElementById('toggle-btn');

    toggleBtn.addEventListener('click', () => {
      wrapper.classList.toggle('sidebar-hidden');
    });
  </script>
</body>
</html>
