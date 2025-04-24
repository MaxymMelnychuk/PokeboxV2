<?php
header('Content-Type: application/json');


// Vérifie si un ID a été passé en paramètre
$pokemonId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$pokemonId) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucun ID de Pokémon fourni']);
    exit;
}

// URL vers le Pokémon spécifique
$url = "https://pokeapi.co/api/v2/pokemon/{$pokemonId}";

// Requête HTTP
$response = file_get_contents($url);

// Gestion des erreurs
if ($response === false) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de la récupération des données du Pokémon"]);
    exit;
}

// Envoie les données JSON
echo $response;
?>
