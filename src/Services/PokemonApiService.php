<?php
use GuzzleHttp\Client;

class PokemonApiService {
    private $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function getPokemon($id) {
        $response = $this->client->request('GET', "https://pokeapi.co/api/v2/pokemon/$id", [
            'verify' => false
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        return [
            'name' => $data['name'],
            'hp' => $data['stats'][0]['base_stat'],
            'atk' => $data['stats'][1]['base_stat'],
            'def' => $data['stats'][2]['base_stat'],
            'url' => $data['sprites']['front_default']
        ];
    }

    public function getMultiplePokemon($ids) {
        $promises = array_map(function ($id) {
            return $this->getPokemon($id);
        }, $ids);

        return $promises;
    }
} 