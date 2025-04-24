<?php 

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
        <li><a href="#">Déconnexion</a></li>
      </ul>
    </nav>

    <!-- Contenu principal -->
    <main class="main-content">
      <button id="toggle-btn" class="toggle-btn">&#9776;</button>
      <h1>Welcome !</h1>
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
