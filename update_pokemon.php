<?php

///// dashboard
require_once("connexion.php");

// Charge les dépendances Composer (pour GuzzleHttp)
require 'vendor/autoload.php';

// Utilise la librairie Guzzle pour les requêtes HTTP
use GuzzleHttp\Client;

// Initialisation du client HTTP
$client = new Client();

$ids = range(1, 10); // Crée les IDs de Pokémon 1 à 10

// Boucle sur chaque ID pour envoyer une requête pour chaque Pokémon
foreach ($ids as $id) {

    $response = $client->request('GET', "https://pokeapi.co/api/v2/pokemon/$id", [
        'verify' => false, // Désactive la vérification SSL
    ]);

  
    $statusCode = $response->getStatusCode();
    $body = $response->getBody(); // Corps de la réponse
    $data = json_decode($body, true); // Conversion JSON en tableau PHP

    // Traitement des données pour insérer dans la base de données
    $name = $data['name'];
    $hp = $data['stats'][0]['base_stat'];
    $atk = $data['stats'][1]['base_stat'];
    $def = $data['stats'][2]['base_stat'];
    $url = $data['sprites']['other']['official-artwork']['front_default'];

    try {
        $stmt = $pdo->prepare("INSERT INTO cards (name, hp, atk, def, url) VALUES( :name, :hp, :atk, :def, :url)");

        $stmt->execute([
            "name" => $name,
            "hp" => $hp,
            "atk" => $atk,
            "def" => $def,
            "url" => $url
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}




?>