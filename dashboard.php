<?php
// ce que lon va mettre dans message, de ce que lon va parler, de 404Systeme

//fasse videos a la place des ateliers

//helper qui occupe des questions
//idées du helpeer projet
//envoi

//reu nico
//distanciel

//message 6 juin




///// dashboard
require_once("connexion.php");

// Charge les dépendances Composer (pour GuzzleHttp)
require 'vendor/autoload.php';

// Utilise la librairie Guzzle pour les requêtes HTTP
use GuzzleHttp\Client;

// Initialisation du client HTTP
$client = new Client();
// header('Content-Type: application/json');
$ids = [];

for ($i= 1; $i <= 10 ; $i++) { 
  $ids[] = $i;
}



$promises = array_map(function ($id) use ($client) {
  $response = $client->request('GET', "https://pokeapi.co/api/v2/pokemon/$id", [
    'verify' => false, // Désactive la vérification SSL 
    
  ]);
  
  $statusCode = $response->getStatusCode(); 
  $body = $response->getBody(); // Corps de la réponse
  $data = json_decode($body, true); // Conversion JSON en tableau PHP
  return $data;
  
}, $ids);


// envoi BDD



  // $name =  $pokemon['name'];
  // $hp = $pokemon['stats'][0]['base_stat'];
  // $atk = $pokemon['stats'][1]['base_stat'];
  // $def = $pokemon['stats'][2]['base_stat']; //defini avec post
 




  // try { //inserer dans la BDD ce qu'on avait mit dans input
   
  //   $stmt = $pdo->query("SELECT * FROM cards");


        
  //     } catch (PDOException $e) {
  //       echo $e->getMessage();
  // }






  $time = null;
   $cards = [];

  if (isset($_GET['action']) && $_GET['action'] == 'open') {
   
    $name = $_SESSION['username'];

    if (!isset($_SESSION['dernier'])) {
        $_SESSION['dernier'] = time();
    }

    $time = time() - $_SESSION['dernier'];

    if ($time < 10) {
        //echo "Attends encore " . (10 - $time) . " secondes.";
    } else {
        $_SESSION['dernier'] = time(); 

        try {
            $stmt = $pdo->query("SELECT * FROM cards ORDER BY RAND() LIMIT 5");
            $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
          var_dump($cards);
            $insert = $pdo->prepare("INSERT INTO cards_users (card_name, user_name, url) VALUES (:card_name, :user_name, :url)");
           foreach ($cards as $card) {
            $cardName = $card['name'];  
            $url = $card['url'];

            $sql = "SELECT COUNT(*) FROM cards_users WHERE card_name = :card_name AND user_name = :user_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
            ':card_name' => $cardName,
            ':user_name' => $name
            ]);

            $count = $stmt->fetchColumn(); //1

            if ($count > 0) { //si carte existe deja
              $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity + 1 WHERE card_name = :card_name AND user_name = :user_name");
              $stmt->execute([
                ':card_name' => $cardName,
                ':user_name' => $name
              ]);
            } else {
              
              $insert->execute([
              ':card_name' => $cardName,   // variable avec le nom de la carte
              ':user_name' => $name,   // variable avec le nom de l'utilisateur
              ':url'       => $url         // variable avec l'URL
              ]);

              echo '<pre>';
              print_r($count);
              echo '</pre>';

            }
 
}
           
            $_SESSION['last_cards'] = $cards;
            header('Location: dashboard.php');
            exit;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

} else {
    // $stmt = $pdo->query("SELECT * FROM cards");
    // $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//selection de 
  if (isset($_GET['page']) && $_GET['page'] == 'cartes') {

    $name = $_SESSION['username'];

     try {  
           $sql = "SELECT * FROM cards_users WHERE user_name = :user_name";

         
      
            $cards_show = $pdo->prepare($sql);
            $cards_show->execute([':user_name' => $name]);
            $results = $cards_show->fetchAll();
  
//             echo '<pre>';
// print_r($results);
// echo '</pre>';

           
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
  }


// https://potterapi-fedeperin.vercel.app/en/characters











/////////// DESCONNEXION ///////////

if(!isset($_SESSION["iduser"])) {
    header("location:login.php");
}



if(isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    // je vide ma session
    session_unset();
    session_destroy();
    unset($_SESSION["iduser"]);
    unset($_SESSION["email"]);
    unset($_SESSION["username"]);
    unset($_SESSION['dernier']);
    header("location:login.php"); // redirection sans paramètre
    exit;
}


//  -- 0 = pending, 1 = accepted, 2 = refused, 3 = blocked

$friends = [];
$alreadyInvited = [];
$receivedInvites = [];


if (isset($_GET['page']) && $_GET['page'] == 'amis') {
// objet en tableau

  $input = file_get_contents('php://input');
        $data = json_decode($input, true);

    $pending = 0;
    $userId = $_SESSION['iduser'];


    // post du js
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        

        // Cas invitation
    if (isset($data['inviteUserId'])) {
        $receiverId = (int)$data['inviteUserId'];
       

        $sql = "INSERT INTO friendships (sender_id, receiver_id, status) 
                VALUES (:sender_id, :receiver_id, :status)";
        $invite = $pdo->prepare($sql);
        $invite->execute([
            ':sender_id' => $userId,
            ':receiver_id' => $receiverId,
            ':status' => $pending
        ]);

        echo json_encode(['success' => true]);
        exit;
    }

       // Cas annulation
    if (isset($data['cancelInviteUserId'])) {
        $receiverId = (int)$data['cancelInviteUserId'];

        $sql = "DELETE FROM friendships 
                WHERE sender_id = :sender_id 
                  AND receiver_id = :receiver_id 
                  AND status = 0";  // ici 0 = invitation en attente
        $cancel = $pdo->prepare($sql);
        $cancel->execute([
            ':sender_id' => $userId,
            ':receiver_id' => $receiverId
        ]);

        echo json_encode(['success' => true]);
        exit;
    }

    // Accepter une demande
if (isset($data['acceptInviteUserId'])) {
    $senderId = (int)$data['acceptInviteUserId'];
    $sql = "UPDATE friendships SET status = 1 WHERE sender_id = :sender AND receiver_id = :receiver AND status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['sender' => $senderId, 'receiver' => $userId]);
    echo json_encode(['success' => true]);
    exit;
}

// Refuser une demande
if (isset($data['refuseInviteUserId'])) {
    $senderId = (int)$data['refuseInviteUserId'];
    $sql = "DELETE FROM friendships WHERE sender_id = :sender AND receiver_id = :receiver AND status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['sender' => $senderId, 'receiver' => $userId]);
    echo json_encode(['success' => true]);
    exit;
}

    }


  try {
       $stmt2 = $pdo->prepare("SELECT iduser, username FROM user WHERE iduser != :me");
$stmt2->execute(['me' => $userId]);
$friends = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT receiver_id, status
                               FROM friendships
                               WHERE  sender_id = :sender_id AND status = :status");
        $stmt->execute(['sender_id' => $userId,
        'status' => $pending
      ]);
        $alreadyInvited = $stmt->fetchAll(PDO::FETCH_COLUMN);
        // var_dump($alreadyInvited);

        // foreach ($invite as $invi) {
        //   if ($invi['status'] == 0) {
        //     echo "Invitation enviyer"; 
        //   }
        // }

        $stmt2 = $pdo->prepare("SELECT sender_id, status
                               FROM friendships
                               WHERE  receiver_id = :receiver_id AND status = :status");
        $stmt2->execute(['receiver_id' => $userId,
        'status' => $pending
      ]);
        $receivedInvites = $stmt2->fetchAll(PDO::FETCH_COLUMN);
      // var_dump($receivedInvites);
       

    } catch (PDOException $e) {
        echo 'Erreur BDD : ' . $e->getMessage();
    }

    

} else {
  // Sinon, c’est GET → on affiche les utilisateurs

    
}






















?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="wrapper" id="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <ul>
        <li><a class="bg" href="dashboard.php">Profil</a></li>
        <li><a class="bg" href="?page=cartes">Mes cartes</a></li>
        <li><a class="bg" href="?page=amis">Amis</a></li>
        <li><a class="bg" href="?action=deconnexion">Déconnexion</a></li>
      </ul>
    </nav>

  
    
    <!-- Contenu principal -->
    <main class="main-content">
    <button id="toggle-btn" class="toggle-btn">&#9776;</button>
      <?php
    // if ($time <= 10 && $time == null  ) {
    //   echo "Tu peut ouvrir booster";
    // } else {
    //   echo " Attends encore " . (10 - $time) . " secondes.";
    // }




      // $response = rand(1, 100);
     


  if (!isset($_GET["page"])) {
    echo "<h1> Salut,  " . $_SESSION["username"] . "</h1>";

     ?>

    <p></p>
      <a id="open" class="btn_open" href="?action=open">Open booster</a>
      <p id="timer" ></p>
      <h1 class="centered">Pokemon Cards !</h1> 
      
    <div id="pokemonCards">
    <?php 
    // echo $i;
    // echo implode(', ', range(0, 5));
    // Boucle d'affichage des personnages
    if (isset($_SESSION['last_cards'])) {
      foreach($_SESSION['last_cards'] as $card) {
         echo "<div class='pokemon'>"; // Conteneur par pokemon
        
        // Affichage de l'image
        echo '<img class="image-pokemon" src="' . $card['url'] . '">';

        // Affichage du nom
          echo '<div class="description">';
            echo '<div class="name">' . $card['name'] . '</div>';
            echo '<div class="stats">';
              echo '<div class="stat">' . "hp : " . $card['hp']  . '</div>';
              echo '<div class="stat">' . "atk : " . $card['atk']  . '</div>';
              echo '<div class="stat">' . "def : " . $card['def']  . '</div>';
            echo '</div>';
          echo '</div>';
        echo "</div>";
        
      }
    }
  } else if  ($_GET["page"] == "cartes") {
    echo "<h1>Mes cartes</h1>";
                if (empty($results)) {
    echo "<p>Tu n'as encore aucune carte.</p>";
}
    ?>
    <div id="pokemonCards">

    <?php

    foreach($results as $result) {
         echo "<div class='pokemon'>"; // Conteneur par pokemon
        
      
             echo '<img class="image-pokemon" src="' . $result['url'] . '">';
              echo '<div class="stat">'  . $result['card_name']  . '</div>';
               echo '<div class="stat">'. "Quantité : "  . $result['quantity']  . '</div>';
              
           

        
      echo "</div>";
      }

   ?>
  </div>
  <?php

    
       
    

// echo "<pre>";
// print_r($promises);
// echo "</pre>";
//     print_r($ids);
   

    
  } else if ($_GET["page"] == "amis") {
     echo "<h1>Tes amis :</h1>";
     echo "<div id='friends-list'></div>";
     ?>
       
           

        <?php
      
      }
  


 ?>
    
    </div>
    

    <div id="inventory">
  <div class="card" data-id="1">Carte 1</div>
  <div class="card" data-id="2">Carte 2</div>
  <div class="card" data-id="3">Carte 3</div>
  <!-- Ajoute d'autres cartes ici -->
</div>

<div id="trade-zone">
  <h3>Zone d'Échange</h3>
  <!-- Les cartes sélectionnées apparaîtront ici -->
</div>
      
    
    
    </main>
  </div>

  <script>
const cards = document.querySelectorAll('.card');
const tradeZone = document.getElementById('trade-zone');

cards.forEach(card => {
  card.addEventListener('click', () => {
    card.classList.toggle('selected');
    updateTradeZone();
  });
});

function updateTradeZone() {
  const selectedCards = document.querySelectorAll('.card.selected');
  tradeZone.innerHTML = '<h3>Zone d\'Échange</h3>'; // Réinitialise la zone d'échange

  selectedCards.forEach(card => {
    const cardClone = card.cloneNode(true);
    cardClone.addEventListener('click', () => {
      cardClone.classList.toggle('selected');
      updateTradeZone();
    });
    tradeZone.appendChild(cardClone);
  });
}






    const wrapper = document.getElementById('wrapper');
    const toggleBtn = document.getElementById('toggle-btn');

    //changement dynamiquement sans recharger la page
    // colonne  que avec user id, pourquoi avec acces de friend au iduser
     const friends = <?= json_encode($friends); ?>;
  const alreadyInvited = <?= json_encode($alreadyInvited); ?>;
  const receivedInvites = <?= json_encode($receivedInvites); ?>;

  const container = document.getElementById('friends-list');

  friends.forEach(friend => {
    const isInvited = alreadyInvited.includes(friend.iduser);
    const hasInvitedMe = receivedInvites.includes(friend.iduser);

    const wraping = document.createElement('div');
    wraping.classList.add('wraping');

    const statDiv = document.createElement('div');
    statDiv.textContent = friend.username;
    wraping.appendChild(statDiv);

    if (hasInvitedMe) {
      // Accepter / Refuser
      const acceptBtn = document.createElement('button');
      acceptBtn.textContent = "Accepter";
      acceptBtn.dataset.userid = friend.iduser;
      acceptBtn.classList.add('invite');
      wraping.appendChild(acceptBtn);

      const refuseBtn = document.createElement('button');
      refuseBtn.textContent = "Refuser";
      refuseBtn.dataset.userid = friend.iduser;
      refuseBtn.classList.add('cancel');
      wraping.appendChild(refuseBtn);

    } else if (isInvited) {
     
      const inviteBtn = document.createElement('button');
      inviteBtn.textContent = "Invitation envoyée";
      inviteBtn.classList.add('invite');
      inviteBtn.disabled = true;
      wraping.appendChild(inviteBtn);

      const cancelBtn = document.createElement('button');
      cancelBtn.textContent = "Annuler";
      cancelBtn.classList.add('cancel');
      cancelBtn.dataset.userid = friend.iduser;
      wraping.appendChild(cancelBtn);

    } else {
      
      const inviteBtn = document.createElement('button');
      inviteBtn.textContent = "Invite";
      inviteBtn.classList.add('invite');
      inviteBtn.dataset.userid = friend.iduser;
      wraping.appendChild(inviteBtn);
    }

    container.appendChild(wraping);
  });


 //envoi au click
  container.addEventListener('click', (event) => {
    const target = event.target;

   //inviter
    if (target.classList.contains('invite') && target.textContent.trim().toLowerCase() === "invite") {
      const userId = target.dataset.userid;
      fetch('dashboard.php?page=amis', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ inviteUserId: userId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          target.textContent = "Invitation envoyée";
          target.disabled = true;
         

          const cancelBtn = document.createElement('button');
          cancelBtn.classList.add('cancel');
          cancelBtn.dataset.userid = userId;
          cancelBtn.textContent = 'Cancel';
          target.parentNode.appendChild(cancelBtn);
        } else {
          alert("Erreur : " + JSON.stringify(data));
        }
      })
      .catch(console.error);
    }

    //annuler invitation
    else if (target.classList.contains('cancel') && (target.textContent === "Cancel" || target.textContent === "Annuler")) {
      const userId = target.dataset.userid;
      fetch('dashboard.php?page=amis', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ cancelInviteUserId: userId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const inviteBtn = target.parentNode.querySelector('.invite');
          if (inviteBtn) {
            inviteBtn.textContent = "Invite";
            inviteBtn.disabled = false;
          }
          target.remove();
        } else {
          alert("Erreur lors de l'annulation");
        }
      })
      .catch(console.error);
    }

    // Accepter invitation
    else if (target.classList.contains('invite') && target.textContent === "Accepter") {
      const userId = target.dataset.userid;
      fetch('dashboard.php?page=amis', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ acceptInviteUserId: userId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          target.textContent = "Ami(e)";
          const refuseBtn = target.parentNode.querySelector('.cancel');
          if (refuseBtn) refuseBtn.remove();
        }
      })
      .catch(console.error);
    }

    // Refuser invitation
    else if (target.classList.contains('cancel') && target.textContent === "Refuser") {
      const userId = target.dataset.userid;
      fetch('dashboard.php?page=amis', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ refuseInviteUserId: userId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const acceptBtn = target.parentNode.querySelector('.invite');
          if (acceptBtn) acceptBtn.remove();
          target.remove();
        }
      })
      .catch(console.error);
    }
  });



    toggleBtn.addEventListener('click', () => {
      wrapper.classList.toggle('sidebar-hidden');
    });


   
    let isFirstTime = localStorage.getItem("alreadyStarted") !== "true";
    let sum = parseInt(localStorage.getItem("finCooldown"));;

    let timer = setInterval(myFunc, 1000);
   

    // La variable qui suit si c'est la première fois

    if (isFirstTime) {
  console.log("Première exécution, donc true");
  sum = 11;

  localStorage.setItem("alreadyStarted", "true");
  localStorage.setItem("finCooldown", sum);
} else {
  console.log("Déjà exécuté, on garde la valeur existante");
}
    

    localStorage.setItem("finCooldown", sum);



    function myFunc(p1) {
      let stock  = parseInt(localStorage.getItem("finCooldown"));
      
       stock -= 1;
       parseInt(localStorage.setItem("finCooldown", stock));
       document.getElementById("timer").textContent = `${stock}`;

       if (stock <= 0) {
        clearInterval(timer)
        document.getElementById("timer").textContent = `Tu peut ouvrir un booster`;
        document.getElementById("open").style.display = "block";
      
        localStorage.removeItem("alreadyStarted");
        
       }
    } 

  </script>
</body>
</html>
