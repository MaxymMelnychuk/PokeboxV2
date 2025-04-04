<?php 
require 'vendor/autoload.php'; // Inclure l'autoload de Composer

use GuzzleHttp\Client;

// Créer un client HTTP
$client = new Client();

// Faire une requête GET
$response = $client->request('GET', 'https://pokeapi.co/api/v2/pokemon', [
    'verify' => false // Désactiver la vérification SSL
]);


$status = $response->getStatusCode();

$body = $response->getBody();

$data = json_decode($body, true);

header('Content-Type: application/json');

?>