<?php

// ce que lon va mettre dans message, de ce que lon va parler, de 404Systeme

//fasse videos a la place des ateliers

//helper qui occupe des questions
//idées du helpeer projet
//envoi
//qui reste ou non
//si aimer cette anneé
//reu nico
//distanciel

//message 6 juin

//AG est il bien si on annonce 

//est ce que vous aussi vous voulez si vous parler au debut de AG, et ensuite vous passez la parole ensuite pour annonce



///// dashboard
require_once("../../config/database.php");

// Charge les dépendances Composer (pour GuzzleHttp)
// require_once("../../vendor/autoload.php");

//amitié
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['inviteUserId'])) {
        header('Content-Type: application/json');
        try {
            $stmt = $pdo->prepare("INSERT INTO friendships (sender_id, receiver_id, status) VALUES (?, ?, 0)");
            $stmt->execute([$_SESSION['iduser'], $data['inviteUserId']]);
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Utilise la librairie Guzzle pour les requêtes HTTP
// use GuzzleHttp\Client;

// // Initialisation du client HTTP
// $client = new Client();
// // header('Content-Type: application/json');
// $ids = [];

// for ($i= 1; $i <= 10 ; $i++) { 
//   $ids[] = $i;
// }



// $promises = array_map(function ($id) use ($client) {
//   $response = $client->request('GET', "https://pokeapi.co/api/v2/pokemon/$id", [
//     'verify' => false, // Désactive la vérification SSL 
    
//   ]);
  
//   $statusCode = $response->getStatusCode(); 
//   $body = $response->getBody(); // Corps de la réponse
//   $data = json_decode($body, true); // Conversion JSON en tableau PHP
//   return $data;
  
// }, $ids);


// envoi BDD



  // $name =  $pokemon['name'];
  // $hp = $pokemon['stats'][0]['base_stat'];
  // $atk = $pokemon['stats'][1]['base_stat'];
  // $def = $pokemon['stats'][2]['base_stat']; //defini avec post
 

//si existe pas ou il est null
if (!isset($results) || !is_array($results)) {
    $results = [];
}

  // try { //inserer dans la BDD ce qu'on avait mit dans input
   
  //   $stmt = $pdo->query("SELECT * FROM cards");


        
  //     } catch (PDOException $e) {
  //       echo $e->getMessage();
  // }



// SELECT COUNT(name) 
// FROM cards;

//compteur combien de cartes jai obtenu
$name = $_SESSION['username'];
$cards = $pdo->prepare("SELECT COUNT(user_name) FROM cards_users WHERE user_name = :username");
$cards->execute([
  'username' => $name
]);
$obtained = $cards->fetchColumn();

// Débogage
if ($obtained === false) {
    $obtained = 0; // si rien, on met zero
}

// if (!isset($_GET["page"])) {
//   $name = $_SESSION['username'];
//   $cards = $pdo->prepare("SELECT COUNT(user_name) FROM cards_users WHERE user_name = :username");
//   $cards->execute([
//     'username' => $name
//   ]);
//   $obtained = $cards->fetchColumn();
// }


  $time = null;
   $cards = [];

  if (isset($_GET['action']) && $_GET['action'] == 'open') {
   
    $name = $_SESSION['username'];

    if (!isset($_SESSION['dernier'])) {
      $_SESSION['dernier'] = time(); // //premier fois
      $time = 0; 
  } else {
      $time = time() - $_SESSION['dernier']; 
  }

    if ($time < 10) {
      echo "attendre encore :" . $time;
    } else {
        $_SESSION['dernier'] = time(); 

        try {
           //5 cartes random
            $stmt = $pdo->query("SELECT atk, name, hp, def, url FROM cards ORDER BY RAND() LIMIT 5");
            $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
          

            if (empty($cards)) {
              
                $baseCards = [
                    ['name' => 'Pikachu', 'url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png', 'hp' => 35, 'atk' => 55, 'def' => 40],
                    ['name' => 'Bulbasaur', 'url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png', 'hp' => 45, 'atk' => 49, 'def' => 49],
                    ['name' => 'Charmander', 'url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/4.png', 'hp' => 39, 'atk' => 52, 'def' => 43],
                    ['name' => 'Squirtle', 'url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/7.png', 'hp' => 44, 'atk' => 48, 'def' => 65],
                    ['name' => 'Eevee', 'url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/133.png', 'hp' => 55, 'atk' => 55, 'def' => 50]
                ];

                $insertStmt = $pdo->prepare("INSERT INTO cards (name, url, hp, atk, def) VALUES (?, ?, ?, ?, ?)");
                foreach ($baseCards as $card) {
                    $insertStmt->execute([$card['name'], $card['url'], $card['hp'], $card['atk'], $card['def']]);
                }

             
                $stmt = $pdo->query("SELECT * FROM cards ORDER BY RAND() LIMIT 5");
                $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // stocker les cartes
            $_SESSION['last_cards'] = $cards;
            
            foreach ($cards as $card) {
                $cardName = $card['name'];  
                $url = $card['url'];

                //selectionne nombre de cartes et ou egal a ma session

                $sql = "SELECT COUNT(*) FROM cards_users WHERE card_name = :card_name AND user_name = :user_name";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':card_name' => $cardName,
                    ':user_name' => $name
                ]);

                $count = $stmt->fetchColumn();

                //et si ya carte existe, on juste rajoute en quantité

                if ($count > 0) {
                    $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity + 1 WHERE card_name = :card_name AND user_name = :user_name");
                    $stmt->execute([
                        ':card_name' => $cardName,
                        ':user_name' => $name
                    ]);
                } else { //et si existe pas encire, on insere nouvelle carte avec nom de ssenio et quantité 1
                    $insert = $pdo->prepare("INSERT INTO cards_users (card_name, user_name, url, quantity) VALUES (:card_name, :user_name, :url, 1)");
                    $insert->execute([
                        ':card_name' => $cardName,
                        ':user_name' => $name,
                        ':url'       => $url
                    ]);
                }
            }
            //apres quand on a ouvrir booster rediurection vers dashbord
            header('Location: dashboard.php');
            exit; //pourquil tourne pas

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

} else {
    // $stmt = $pdo->query("SELECT * FROM cards");
    // $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//si page est ami ou cartes
  if (isset($_GET['page']) && ($_GET['page'] == 'amis' || $_GET['page'] == 'cartes')) {

    $name = $_SESSION['username'];

    //si je lavais envoyer une requette  avec js
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
      // recupere donée  json
        $input = file_get_contents('php://input');
      // convertt au tableau associatif pour faciliter lecture
        $data = json_decode($input, true);

        if (isset($data['toggleLike'])) {
            header('Content-Type: application/json');
            try {
                $stmt = $pdo->prepare("UPDATE cards_users SET liked = :liked WHERE user_name = :user_name AND card_name = :card_name");
                $stmt->execute([
                    ':liked' => $data['liked'] ? 1 : 0, //si likée 1, sinon 0
                    ':user_name' => $name,
                    ':card_name' => $data['cardName']
                ]);
                
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }
    }

    try {  //selectionne des cartes de cards userrs et cards donées
           $sql = "SELECT pokemon.*, cards.hp, cards.atk, cards.def
            FROM cards_users pokemon, cards
            WHERE pokemon.card_name = cards.name 
            AND pokemon.user_name = :user_name";

         
      
            $cards_show = $pdo->prepare($sql);
            $cards_show->execute([':user_name' => $name]);
            $results = $cards_show->fetchAll();






        // arecuperation cartes dautres personnes
           $sql2 = "SELECT * FROM cards_users WHERE user_name != :me";
          $cards_friend = $pdo->prepare($sql2);
          $cards_friend->execute([':me' => $name]);
          $results2 = $cards_friend->fetchAll();
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
    header("location:../auth/login.php");
}



if(isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    // je vide ma session
    session_unset();
    session_destroy();
    unset($_SESSION["iduser"]);
    unset($_SESSION["email"]);
    unset($_SESSION["username"]);
    unset($_SESSION['dernier']);
    header("location:../auth/login.php"); // redirection sans paramètre
    exit;
}


//  -- 0 = pending, 1 = accepted
$alreadyFriends = [];
$friends = [];
$alreadyInvited = [];
$receivedInvites = [];

// Initialisation des variables
$pendingTrades = [];

if (isset($_GET['page']) && $_GET['page'] == 'amis') {
    $userId = $_SESSION['iduser'];
    $me = $_SESSION['username'];
    //selectionne des tardes en attente
    $stmt = $pdo->prepare("
        SELECT id, sender_name, receiver_name, status, created_at 
        FROM trades 
        WHERE (sender_name = :username OR receiver_name = :username) 
        AND status = 0
    ");
    $stmt->execute(['username' => $me]);
    $pendingTrades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $input = file_get_contents('php://input');
        $data = json_decode($input, true);

    $pending = 0;
   


    // post du js
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['trade'])) { //si bouton trade
      //recup donées mit dans zones de echange
        $me = $data['myUsername']; //moi
        $friend = $data['friendName']; //avec qui je eahcnge
        $myCards = $data['myCards']; //mes cartes
        $friendCards = $data['friendCards']; //cartes de ami

        //insere les trades
        $stmt = $pdo->prepare("INSERT INTO trades (sender_name, receiver_name, status) VALUES (?, ?, 0)");
        $stmt->execute([$me, $friend]);
        $tradeId = $pdo->lastInsertId();
        //auto increment id de trade

        //insere de differentes cartes
        foreach ($myCards as $card) {
            $stmt = $pdo->prepare("INSERT INTO trading_cards (trade_id, card_name, card_quantity_sender) VALUES (?, ?, ?)");
            $stmt->execute([$tradeId, $card['name'], $card['quantity']]);
        }

        foreach ($friendCards as $card) {
            $stmt = $pdo->prepare("INSERT INTO trading_cards (trade_id, card_name, card_quantity_friend) VALUES (?, ?, ?)");
            $stmt->execute([$tradeId, $card['name'], $card['quantity']]);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    // Annuler un trade
    if (isset($data['cancelTradeId'])) {
        $tradeId = (int)$data['cancelTradeId'];

        // supprime le trade avce cartes que je veut
        $stmt = $pdo->prepare("DELETE FROM trading_cards WHERE trade_id = ?");
        $stmt->execute([$tradeId]);

        // suprime aussi de tardes
        $stmt = $pdo->prepare("DELETE FROM trades WHERE id = ?");
        $stmt->execute([$tradeId]);

        echo json_encode(['success' => true]);
        exit;
    }

    // Valider un trade
    if (isset($data['acceptTradeId'])) {
        $tradeId = (int)$data['acceptTradeId'];

        // recupere qui envoyer demande de trade 
        $stmt = $pdo->prepare("SELECT sender_name, receiver_name FROM trades WHERE id = ?");
        $stmt->execute([$tradeId]);
        $trade = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trade) {
            echo json_encode(['success' => false, 'message' => 'Trade non trouvé']);
            exit;
        }

        $sender = $trade['sender_name'];
        $receiver = $trade['receiver_name'];

        // Récupérer les cartes proposées
        $stmt = $pdo->prepare("SELECT card_name, card_quantity_sender, card_quantity_friend FROM trading_cards WHERE trade_id = ?");
        $stmt->execute([$tradeId]);
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //commence de translmettres les cartes
        $pdo->beginTransaction();

        try {
            // pour chaque carte retirer du sender puis ajouter au receiver
            // et inversement

            foreach ($cards as $card) {
                $name = $card['card_name'];
                $qtySender = (int)$card['card_quantity_sender'];
                $qtyFriend = (int)$card['card_quantity_friend'];

                // retire cartes du sender
                if ($qtySender > 0) {
                    $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity - ? WHERE user_name = ? AND card_name = ?");
                    $stmt->execute([$qtySender, $sender, $name]);
                }
                // et ajoute au receiver
                if ($qtySender > 0) {
                    
                    $stmt = $pdo->prepare("SELECT id FROM cards_users WHERE user_name = ? AND card_name = ?");
                    $stmt->execute([$receiver, $name]);
                    if ($stmt->fetch()) {
                        $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity + ? WHERE user_name = ? AND card_name = ?");
                        $stmt->execute([$qtySender, $receiver, $name]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO cards_users (user_name, card_name, quantity) VALUES (?, ?, ?)");
                        $stmt->execute([$receiver, $name, $qtySender]);
                    }
                }

                // meme chose mais linverse
                if ($qtyFriend > 0) {
                    $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity - ? WHERE user_name = ? AND card_name = ?");
                    $stmt->execute([$qtyFriend, $receiver, $name]);

                    $stmt = $pdo->prepare("SELECT id FROM cards_users WHERE user_name = ? AND card_name = ?");
                    $stmt->execute([$sender, $name]);
                    if ($stmt->fetch()) {
                        $stmt = $pdo->prepare("UPDATE cards_users SET quantity = quantity + ? WHERE user_name = ? AND card_name = ?");
                        $stmt->execute([$qtyFriend, $sender, $name]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO cards_users (user_name, card_name, quantity) VALUES (?, ?, ?)");
                        $stmt->execute([$sender, $name, $qtyFriend]);
                    }
                }
            }

            // si tout est bon on met 1
            $stmt = $pdo->prepare("UPDATE trades SET status = 1 WHERE id = ?");
            $stmt->execute([$tradeId]);
            //valide trade
            $pdo->commit();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack(); //sinon annule trade en revennont en arriere
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // recupere toutes trades en attente
    if (isset($data['getPendingTrades'])) {
        $userName = $data['username'];

        $stmt = $pdo->prepare("SELECT * FROM trades WHERE (sender_name = ? OR receiver_name = ?) AND status = 0");
        $stmt->execute([$userName, $userName]);
        $trades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($trades as $trade) {
            $tradeId = $trade['id'];

            $stmt = $pdo->prepare("SELECT card_name, card_quantity_sender, card_quantity_friend FROM trading_cards WHERE trade_id = ?");
            $stmt->execute([$tradeId]);
            $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result[] = [
                'trade' => $trade,
                'cards' => $cards
            ];
        }

        echo json_encode(['success' => true, 'trades' => $result]);
        exit;
    }
}


  try { //recup toutes utilisateurs pour afficher dans mes utilisateurs
       $stmt2 = $pdo->prepare("SELECT iduser, username FROM user WHERE iduser != :me");
$stmt2->execute(['me' => $userId]);
$friends = $stmt2->fetchAll(PDO::FETCH_ASSOC);
//invitations que jai envoyée
        $stmt = $pdo->prepare("SELECT receiver_id, status
                               FROM friendships
                               WHERE  sender_id = :sender_id AND status = :status");
        $stmt->execute(['sender_id' => $userId,
        'status' => $pending
      ]);
        $alreadyInvited = $stmt->fetchAll(PDO::FETCH_COLUMN);
//recupere mes amis
      $stmt3 = $pdo->prepare("
  SELECT 
    CASE 
      WHEN sender_id = :me THEN receiver_id
      ELSE sender_id
    END 
  FROM friendships 
  WHERE (sender_id = :me OR receiver_id = :me)
    AND status = 1
");
$stmt3->execute(['me' => $userId]);
$alreadyFriends = $stmt3->fetchAll(PDO::FETCH_COLUMN);
        // var_dump($alreadyFriends);

        // foreach ($invite as $invi) {
        //   if ($invi['status'] == 0) {
        //     echo "Invitation enviyer"; 
        //   }
        // }



      //requette pour voir demandes a accepter
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
  // sinon rien?

    
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
   
    <section class="principal-content">
    <header class="content-header ">
      <p class="time"></p>
    <div class="burger">
    <span></span>
    <span></span>
    <span></span>
</div>


    </header>
    <div class="nav-menu"> <!-- nav menu   -->
        <a href="../landing/index.html" class="link-page-menu">HOME</a>
        <a href="dashboard.php" class="link-page-menu">PROFIL</a>
        <a href="?page=cartes" class="link-page-menu">MES CARTES</a>
        <a href="?page=amis" class="link-page-menu">AMIS</a>
        <a href="?action=deconnexion" class="link-page-menu">DECONNEXION</a>
        <div class="mode link-page-menu">
            <p href="" class="darkmode-text">DARK MODE</p>
            <input type="checkbox" id="darkmode-toggle" class="dark-none">
            <label for="darkmode-toggle"></label> <p class="ifTrue">NON</p>
        </div>
        
        
    </div> 

  
    
    <!-- Contenu principal -->
    <main class="main-content">
   
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
    <div class="booster_container">
      <a id="open" class="btn_open openBooster" href="?action=open">Open booster</a>
      <p id="timer" ></p>
      </div>
      
      
    <div id="pokemonCards">
    <?php 
    if (isset($_SESSION['last_cards'])) {

        foreach($_SESSION['last_cards'] as $card) {
          echo "<div class='pokemon'>"; // Conteneur par pokemon
          echo '<img class="image-pokemon" src="' . $card['url'] . '">';
          echo '<div class="name">' . $card['name'] . '</div>';
          
          //stats
          echo '<div class="stats-container">';
    
          echo '<div class="stat-box hp">';
          echo '<span class="stat-label">HP</span>';
          echo '<span class="stat-value">' . $card['hp'] . '</span>';
          echo '</div>';
          
     
          echo '<div class="stat-box atk">';
          echo '<span class="stat-label">ATK</span>';
          echo '<span class="stat-value">' . $card['atk']  . '</span>';
          echo '</div>';
          
        
          echo '<div class="stat-box def">';
          echo '<span class="stat-label">DEF</span>';
          echo '<span class="stat-value">' . $card['def']  . '</span>';
          echo '</div>';
          
          echo '</div>'; 
          
    
          echo '<div class="card-back">';
          echo '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png" alt="Pokemon Card Back">';
          echo '</div>';
         
          echo "</div>";
        }
    }
    ?>
    </div>

     
    </section>

   
    <?php

    

    
       
    

// echo "<pre>";
// print_r($promises);
// echo "</pre>";
//     print_r($ids);
   

    
  } else if ($_GET["page"] == "cartes") {
    echo "<h1>Mes cartes</h1>";
    if (empty($results)) {
        echo "<p>Tu n'as encore aucune carte.</p>";
    }
    ?>
    <div id="pokemonCards">
    <?php
    // trier cartes likée en comparant les 2
    usort($results, function($a, $b) {
        return ($b['liked'] ?? 0) - ($a['liked'] ?? 0);
    });

    foreach($results as $result) {
        echo "<div class='pokemon'>"; 
        echo '<img class="image-pokemon" src="' . $result['url'] . '">';
        echo '<div class="name">' . $result['card_name'] . '</div>';
        
      
        echo '<div class="stats-container">';
        
       
        echo '<div class="stat-box hp">';
        echo '<span class="stat-label">HP</span>';
        echo '<span class="stat-value">' . $result['hp'] . '</span>';
        echo '</div>';
        
      
        echo '<div class="stat-box atk">';
        echo '<span class="stat-label">ATK</span>';
        echo '<span class="stat-value">' . $result['atk'] . '</span>';
        echo '</div>';
        
       
        echo '<div class="stat-box def">';
        echo '<span class="stat-label">DEF</span>';
        echo '<span class="stat-value">' . $result['def'] . '</span>';
        echo '</div>';
        
        echo '</div>'; 
        
        echo '<div class="quantity">Quantité : ' . $result['quantity'] . '</div>';
        
        // ajout  likée sur cartes ou pas
        $heartClass = isset($result['liked']) && $result['liked'] ? 'liked' : '';
        echo '<div class="heart ' . $heartClass . '" data-card="' . $result['card_name'] . '">❤</div>';
        
        echo "</div>";
    }
    ?>
    </div>

     
    </section>

   
    <?php

    

    
       
    

// echo "<pre>";
// print_r($promises);
// echo "</pre>";
//     print_r($ids);
   

    
  } else if ($_GET["page"] == "amis") {
     echo "<h1>Utilisateurs :</h1>";
     echo "<div id='friends-list'></div>";
     ?>
     <div id="pendingTradesContainer"></div>
    
     
       
            <div class="exchange">
        <div class="my_cards">
          <p>Mes cartes</p>
          <div class="place_my_cards"></div>
          <div class="container">
            
   

   
 </div>
        </div>
        <div class="friend_cards">
          <p>Cartes de ami</p>
          <div class="place_friend_cards"></div>
          <div class="container2">
           
           </div>
        </div>
      </div>
    
    </div>

        <?php
      
      }
  


 ?>

   
    
     
    
    
    </main>
    
  </div>
<script src="dashboard.js"></script>
  <script>
//       const tgl = document.querySelectorAll('.openBooster');
//       const ok = document.getElementById('pokemonCards');
//      const booster = document.querySelectorAll('open');
//      tgl.forEach((tg) => {
//       tg.addEventListener('click', () => {
//       ok.classList.toggle('show');
//      })
// });
    
     
//flipper une carte
     document.querySelectorAll('.pokemon').forEach(card => {
    card.addEventListener('click', () => {
        card.classList.toggle('flipped');
    });
});

// montrer heure
   setInterval(() => {
    const date = new Date();
    const heure = String(date.getHours()).padStart(2, '0');    // Ajoute un 0 si < 10
    const minutes = String(date.getMinutes()).padStart(2, '0'); // Ajoute un 0 si < 10
    const secondes = String(date.getSeconds()).padStart(2, '0'); // Ajoute un 0 si < 10
    
    document.querySelector('.time').textContent = `${heure}:${minutes}:${secondes}`;
}, 1000);

  //selectionne de tout ce que jaurias besoin du php
    let pendingTrades = [];
    let currentTradingFriend = null;
    const status_trade = <?= json_encode($pendingTrades) ?>;
    const session_username = <?= json_encode($_SESSION['username']) ?>;
    const cardsData = <?= isset($results) ? json_encode($results) : '[]' ?>;
    const cardsData2 = <?= isset($results2) ? json_encode($results2) : '[]' ?>;
    const cardQuantities = <?= isset($results) ? json_encode(array_column($results, 'quantity')) : '[]' ?>;
    const currentPage = <?= isset($_GET['page']) ? json_encode($_GET['page']) : 'null' ?>;

    // si page amis
    if (currentPage === 'amis') {
      const trade = document.querySelector('.container');
      const trade2 = document.querySelector('.container2');
      const trading = document.querySelectorAll('.trading');
      const friendCards = document.querySelector('.place_friend_cards');
      const myCards = document.querySelector('.place_my_cards');

      trading.forEach((button, index) => {
        const userName = cardsData[index].user_name;

        button.addEventListener('click', () => {
          
        });
      });

      // qunatité a gerer  pour ami des cartes
      cardsData.forEach((card, index) => {
        const cardDiv = document.createElement('div');
        cardDiv.classList.add('pokemon_container');

        const img = document.createElement('img');
        img.src = card.url;
        img.classList.add('mini-pokemon');

        const name = document.createElement('div');
        name.classList.add('stat');
        name.textContent = card.card_name;

        const quantity = document.createElement('div');
        quantity.classList.add('stat');
        quantity.dataset.quantity = card.quantity;
        quantity.textContent = `Quantité : ${card.quantity}`;

        cardDiv.appendChild(img);
        cardDiv.appendChild(name);
        cardDiv.appendChild(quantity);

        cardDiv.hasClonedOnce = false;
        cardDiv.addEventListener('click', () => {
          const quantityDiv = cardDiv.querySelector('[data-quantity]');
          let quantity = parseInt(quantityDiv.dataset.quantity);

          if (cardDiv.parentNode.classList.contains('container')) {
            let quantity = parseInt(quantityDiv.dataset.quantity);
            
            if (quantity > 0) {  
              quantity--;
              quantityDiv.dataset.quantity = quantity;
              quantityDiv.textContent = `Quantité : ${quantity}`;

              if (!cardDiv.hasClonedOnce) {
                const clonedCard = cardDiv.cloneNode(true);
                const clonedQuantityDiv = clonedCard.querySelector('[data-quantity]');
                if (!clonedQuantityDiv) {
                  console.error("clonedQuantityDiv est null, structure clone incorrecte !");
                  return;
                }
                
                clonedQuantityDiv.dataset.quantity = 1;
                clonedQuantityDiv.textContent = `Quantité : 1`;

                clonedCard.addEventListener('click', () => {
                  let clonedQuantity = parseInt(clonedQuantityDiv.dataset.quantity) || 1;
                  clonedQuantity--;
                  clonedQuantityDiv.dataset.quantity = clonedQuantity;
                  clonedQuantityDiv.textContent = `Quantité : ${clonedQuantity}`;

                  let newQuantity = parseInt(quantityDiv.dataset.quantity) || 0;
                  newQuantity++;
                  quantityDiv.dataset.quantity = newQuantity;
                  quantityDiv.textContent = `Quantité : ${newQuantity}`;

                  if (clonedQuantity === 0) {
                    myCards.removeChild(clonedCard);
                    cardDiv.hasClonedOnce = false;
                    cardDiv.clonedCard = null;
                  }

                  if (newQuantity > 0) {
                    cardDiv.style.display = 'flex';
                  }
                });

                myCards.appendChild(clonedCard);
                cardDiv.hasClonedOnce = true;
                cardDiv.clonedCard = clonedCard;
              } else if (cardDiv.hasClonedOnce) {
                const clonedCard = cardDiv.clonedCard;
                const clonedQuantityDiv = clonedCard.querySelector('[data-quantity]');

                let currentQty = parseInt(clonedQuantityDiv.dataset.quantity) || 1;
                currentQty++;
                clonedQuantityDiv.dataset.quantity = currentQty;
                clonedQuantityDiv.textContent = `Quantité : ${currentQty}`;
              }

              if (quantity === 0) {
                cardDiv.style.display = 'none';
              }
            }
          }
        });

        trade.appendChild(cardDiv);
      });
      // utilisateurs (boutons)
      const wrapper = document.getElementById('wrapper');
      const toggleBtn = document.getElementById('toggle-btn');

      const friends = <?= json_encode($friends); ?>;
      const alreadyFriends = <?= json_encode($alreadyFriends); ?>;
      const alreadyInvited = <?= json_encode($alreadyInvited); ?>;
      const receivedInvites = <?= json_encode($receivedInvites); ?>;

      const container = document.getElementById('friends-list');
      
      if (container) {
        friends.forEach(friend => {
          const isInvited = alreadyInvited.includes(friend.iduser);
          const hasInvitedMe = receivedInvites.includes(friend.iduser);
          const allIds = friends.map(f => f.iduser);
          const all = allIds.includes(friend.iduser); 

          const wraping = document.createElement('div');
          wraping.classList.add('wraping');

          const statDiv = document.createElement('div');
          statDiv.textContent = friend.username;
          wraping.appendChild(statDiv);
          // si il ma inviter
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
            // si jai inviter
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

          } else if (alreadyFriends.includes(friend.iduser)) {
            // bouton ami
            const btn = document.createElement('button');
            btn.textContent = "Ami(e)";
            btn.classList.add('invite');
            btn.dataset.userid = friend.iduser;
            btn.disabled = true;
            wraping.appendChild(btn);

            // si il ya un trade en attente avec ce ami
            const pendingTradeWithFriend = status_trade ? status_trade.find(trade => 
              (trade.sender_name === session_username && trade.receiver_name === friend.username) ||
              (trade.receiver_name === session_username && trade.sender_name === friend.username)
            ) : null;

            if (pendingTradeWithFriend) {
              // tri dans sender ou receiver
              const isSender = pendingTradeWithFriend.sender_name === session_username;
              const isReceiver = pendingTradeWithFriend.receiver_name === session_username;

              if (isSender) {
                //en attente si sender
                const waiting = document.createElement('span');
                waiting.textContent = "Trade en attente";
                waiting.classList.add('status-info');
                wraping.appendChild(waiting);

                // annuler trade
                const cancelBtn = document.createElement('button');
                cancelBtn.textContent = "Annuler";
                cancelBtn.classList.add('cancel-trade');
                cancelBtn.dataset.tradeid = pendingTradeWithFriend.id;
                wraping.appendChild(cancelBtn);

                // click annuler qui envoi json au php et annuler dans bddd
                cancelBtn.addEventListener('click', () => {
                  fetch('dashboard.php?page=amis', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                      cancelTradeId: pendingTradeWithFriend.id
                    })
                  })
                  .then(res => res.json())
                  .then(data => {
                    if (data.success) {
                      // supprime boutons
                      waiting.remove();
                      cancelBtn.remove();

                      // et reaffiche bouton trade
                      const tradeBtn = document.createElement('button');
                      tradeBtn.textContent = "Trade";
                      tradeBtn.classList.add('trading');
                      tradeBtn.dataset.userid = friend.username;
                      wraping.appendChild(tradeBtn);

                      // reajout levenement click sur trade :/
                      tradeBtn.addEventListener('click', () => {
                        currentTradingFriend = friend.username;
                        updateConfirmVisibility();
                        setupTradeObserver();
                      });
                    } else {
                      alert("Erreur lors de l'annulation du trade");
                    }
                  })
                  .catch(error => {
                    alert("Erreur lors de l'annulation du trade");
                  });
                });
              } else if (isReceiver) {
                // valider echange si receiver
                const validateBtn = document.createElement('button');
                validateBtn.textContent = `Valider l'échange avec ${friend.username}`;
                validateBtn.classList.add('confirm-trade');
                validateBtn.dataset.tradeid = pendingTradeWithFriend.id;
                wraping.appendChild(validateBtn);

                // pour voir les details
                const detailsBtn = document.createElement('button');
                detailsBtn.textContent = "Voir détails";
                detailsBtn.classList.add('view-details');
                detailsBtn.dataset.tradeid = pendingTradeWithFriend.id;
                wraping.appendChild(detailsBtn);

                detailsBtn.addEventListener('click', () => {
                  // details du trade recup
                  fetch('dashboard.php?page=amis', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ getPendingTrades: true, username: session_username })
                  })
                  .then(res => res.json())
                  .then(data => {
                    if (data.success) {
                      const trade = data.trades.find(t => t.trade.id === pendingTradeWithFriend.id);
                      if (trade) {
                        let message = "Détails de l'échange :\n\n";
                        message += "Cartes proposées par " + trade.trade.sender_name + ":\n";
                        trade.cards.forEach(card => {
                          if (card.card_quantity_sender > 0) {
                            message += `- ${card.card_name} (x${card.card_quantity_sender})\n`;
                          }
                        });
                        message += "\nVos cartes proposées :\n";
                        trade.cards.forEach(card => {
                          if (card.card_quantity_friend > 0) {
                            message += `- ${card.card_name} (x${card.card_quantity_friend})\n`;
                          }
                        });
                        alert(message);
                      }
                    }
                  })
                  .catch(error => {
                    alert("Erreur lors de la récupération des détails");
                  });
                });
              } else {
                // en attente sinon
                const info = document.createElement('span');
                info.textContent = "Trade en attente";
                info.classList.add('status-info');
                wraping.appendChild(info);
              }
            } else {
              // pas de en attente alors trade
              const tradeBtn = document.createElement('button');
              tradeBtn.textContent = "Trade";
              tradeBtn.classList.add('trading');
              tradeBtn.dataset.userid = friend.username;
              wraping.appendChild(tradeBtn);

              tradeBtn.addEventListener('click', () => {
                currentTradingFriend = friend.username;
                updateConfirmVisibility();
                setupTradeObserver(); // pour observer la zone echange
              });
            }
          } else {
            const inviteBtn = document.createElement('button');
            inviteBtn.textContent = "Invite";
            inviteBtn.classList.add('invite');
            inviteBtn.dataset.userid = friend.iduser;
            wraping.appendChild(inviteBtn);
          }

          container.appendChild(wraping);
        });
      }

      //envoi au click
      if (container) {
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
          // si contient trading, alors on affiche cartes dans zone
          else if (target.classList.contains('trading')) {
            const username = target.dataset.userid;
            currentTradingFriend = username;
            
            const filteredCards = cardsData2.filter(card => card.user_name === username);
            trade2.innerHTML = ''; //vidant precedant
            
            filteredCards.forEach((card, index) => {
              const cardDiv = document.createElement('div');
              cardDiv.classList.add('pokemon_container');

              const img = document.createElement('img');
              img.src = card.url;
              img.classList.add('mini-pokemon');

              const name = document.createElement('div');
              name.classList.add('stat');
              name.textContent = card.card_name;

              const quantity = document.createElement('div');
              quantity.classList.add('stat');
              quantity.dataset.quantity = card.quantity;
              quantity.textContent = `Quantité : ${card.quantity}`;

              cardDiv.appendChild(img);
              cardDiv.appendChild(name);
              cardDiv.appendChild(quantity);

              cardDiv.hasClonedOnce = false;
              cardDiv.addEventListener('click', () => {
                const quantityDiv = cardDiv.querySelector('[data-quantity]');
                let quantity = parseInt(quantityDiv.dataset.quantity);

                if (cardDiv.parentNode.classList.contains('container2')) {
                  let quantity = parseInt(quantityDiv.dataset.quantity); // carte origine
                  
                  if (quantity > 0) {  
                    quantity--;
                    quantityDiv.dataset.quantity = quantity;
                    quantityDiv.textContent = `Quantité : ${quantity}`;

                    if (!cardDiv.hasClonedOnce) {
                      const clonedCard = cardDiv.cloneNode(true);
                      const clonedQuantityDiv = clonedCard.querySelector('[data-quantity]');
                      if (!clonedQuantityDiv) {
                        console.error("clonedQuantityDiv est null, structure clone incorrecte !");
                        return;
                      }
                      
                      clonedQuantityDiv.dataset.quantity = 1;
                      clonedQuantityDiv.textContent = `Quantité : 1`;

                      clonedCard.addEventListener('click', () => {
                        let clonedQuantity = parseInt(clonedQuantityDiv.dataset.quantity) || 1;
                        clonedQuantity--;
                        clonedQuantityDiv.dataset.quantity = clonedQuantity;
                        clonedQuantityDiv.textContent = `Quantité : ${clonedQuantity}`;

                        let newQuantity = parseInt(quantityDiv.dataset.quantity) || 0;
                        newQuantity++;
                        quantityDiv.dataset.quantity = newQuantity;
                        quantityDiv.textContent = `Quantité : ${newQuantity}`;

                        if (clonedQuantity === 0) {
                          friendCards.removeChild(clonedCard);
                          cardDiv.hasClonedOnce = false;
                          cardDiv.clonedCard = null;
                        }

                        if (newQuantity > 0) {
                          cardDiv.style.display = 'flex';
                        }
                      });

                      friendCards.appendChild(clonedCard);
                      cardDiv.hasClonedOnce = true;
                      cardDiv.clonedCard = clonedCard;
                    } else if (cardDiv.hasClonedOnce) {
                      const clonedCard = cardDiv.clonedCard;
                      const clonedQuantityDiv = clonedCard.querySelector('[data-quantity]');

                      let currentQty = parseInt(clonedQuantityDiv.dataset.quantity) || 1;
                      currentQty++;
                      clonedQuantityDiv.dataset.quantity = currentQty;
                      clonedQuantityDiv.textContent = `Quantité : ${currentQty}`;
                    }

                    if (quantity === 0) {
                      cardDiv.style.display = 'none';
                    }
                  }
                }
              });

              trade2.appendChild(cardDiv);
            });
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

          // accepter invitation
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
                const trade = document.createElement('button');
                trade.textContent = "Trade";
                trade.classList.add('trading');
                const wraping = document.querySelector('.wraping')
              
                wraping.appendChild(trade);

                const refuseBtn = target.parentNode.querySelector('.cancel');
                if (refuseBtn) refuseBtn.remove();
              }
            })
            .catch(console.error);
          }

          // refuser invitation
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
      }

      toggleBtn.addEventListener('click', () => {
        wrapper.classList.toggle('sidebar-hidden');
      });

      // gerer la visibilté des boutons de confirmation
      function updateConfirmVisibility() {
        const myCards = document.querySelector('.place_my_cards');
        const friendCards = document.querySelector('.place_friend_cards');

        // trouver container de utilisateur avec qui on trade
        const allWrapings = document.querySelectorAll('.wraping');
        const currentWraping = Array.from(allWrapings).find(wrap => 
          wrap.querySelector('div').textContent === currentTradingFriend
        );

        // verifie si dans les deux zones o a bien plus que 1 carte
        const hasMyCards = myCards.children.length > 0;
        const hasFriendCards = friendCards.children.length > 0;

        // supprime lancien bouton si existe
        const oldConfirmBtn = document.querySelector('.confirm-trade-btn');
        if (oldConfirmBtn) {
          oldConfirmBtn.remove();
        }

        // aficher bouton valider ehcnage si de deux cotées au moins une carte
        if (hasMyCards && hasFriendCards && currentWraping) {
          const newConfirmBtn = document.createElement('button');
          newConfirmBtn.textContent = "Valider l'échange";
          newConfirmBtn.classList.add('confirm-trade-btn');
          currentWraping.appendChild(newConfirmBtn);

          // rajout eveneemt click sur vaider echange
          newConfirmBtn.addEventListener('click', () => {
            const myCards = document.querySelector('.place_my_cards').children;
            const friendCards = document.querySelector('.place_friend_cards').children;
            
            const myCardsData = Array.from(myCards).map(card => ({
              name: card.querySelector('.stat').textContent,
              quantity: parseInt(card.querySelector('[data-quantity]').dataset.quantity)
            }));

            const friendCardsData = Array.from(friendCards).map(card => ({
              name: card.querySelector('.stat').textContent,
              quantity: parseInt(card.querySelector('[data-quantity]').dataset.quantity)
            }));

            fetch('dashboard.php?page=amis', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                trade: true,
                myUsername: session_username,
                friendName: currentTradingFriend,
                myCards: myCardsData,
                friendCards: friendCardsData
              })
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                newConfirmBtn.remove(); //envoi les donées dans bdd et ecrit echange validée
                const confirmed = document.createElement('span');
                confirmed.textContent = "Échange validé";
                currentWraping.appendChild(confirmed);
              } else {
                alert("Erreur: " + (data.message || "Erreur inconnue"));
              }
            })
            .catch(console.error);
          });
        }
      }

      // voir les changements des cartes selectionées
      function setupTradeObserver() {
        const myCards = document.querySelector('.place_my_cards');
        const friendCards = document.querySelector('.place_friend_cards');

        // pour voir les changements dans les cartes selectionnes
        const observer = new MutationObserver((mutations) => {
          updateConfirmVisibility();
        });

        // pour voir 2 zones
        if (myCards) {
          observer.observe(myCards, { childList: true });
        }
        if (friendCards) {
          observer.observe(friendCards, { childList: true });
        }
      }

      // appele au chargement de la page
      document.addEventListener('DOMContentLoaded', () => {
        updateConfirmVisibility();
        setupTradeObserver();
      });
    }

    // pour timer
    let openBtn = document.getElementById("open");
    let timerText = document.getElementById("timer");

    if (openBtn && timerText) {
      const cooldownDuration = 10 * 1000; // 10 secondes en millisecondes

      // si il ya deja la fin du timer stockée
      let finCooldown = parseInt(localStorage.getItem("finCooldown")) || 0;

      function updateTimer() {
        const now = Date.now();
        const remaining = finCooldown - now;
        //si il est inf ou egal 0
        if (remaining <= 0) {
          // alors on peut ouvrir un booster
          openBtn.style.display = "block";
          timerText.textContent = "Tu peux ouvrir un booster";
          clearInterval(timerInterval);
        } else { //sinon on attend
          openBtn.style.display = "none";
          const seconds = Math.ceil(remaining / 1000);
          timerText.textContent = `${seconds}s restantes`;
        }
      }

      //interval de 10s
      let timerInterval = setInterval(updateTimer, 1000);
      updateTimer(); // appele le timer

      // quand on click sur bouton ouvrir un booster
      openBtn.addEventListener("click", () => {
        openBtn.style.display = "none";

        // stock nouveau temps de fin
        finCooldown = Date.now() + cooldownDuration;
        localStorage.setItem("finCooldown", finCooldown);

        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
      });
    }

    // gestion des likes
    document.addEventListener('DOMContentLoaded', function() {
      const hearts = document.querySelectorAll('.heart');
      
      hearts.forEach(heart => {
        heart.addEventListener('click', function() {
          const cardName = this.dataset.card;
          const isLiked = this.classList.contains('liked');
          // si contient class liked alors on envoi au php
          fetch('dashboard.php?page=cartes', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              toggleLike: true,
              cardName: cardName,
              liked: !isLiked
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              this.classList.toggle('liked');
              window.location.reload(); //refraichi la page pour voir au tout debut les likes
            } else {
              alert('Erreur lors de la mise à jour du like');
            }
          })
          .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour du like');
          });
        });
      });
    });
  </script>
</body>
</html>
