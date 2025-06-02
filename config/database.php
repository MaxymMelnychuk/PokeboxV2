<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=pokebox;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Créer la table cards si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        hp INT NOT NULL,
        atk INT NOT NULL,
        def INT NOT NULL
    )");

    // Créer la table cards_users si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS cards_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        card_name VARCHAR(255) NOT NULL,
        user_name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        quantity INT DEFAULT 1,
        liked BOOLEAN DEFAULT FALSE,
        UNIQUE KEY unique_card_user (card_name, user_name)
    )");

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
} 
session_start();