<?php
class Card {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRandomCards($limit = 5) {
        $stmt = $this->pdo->query("SELECT * FROM cards ORDER BY RAND() LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserCards($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM cards_users WHERE user_name = ?");
        $stmt->execute([$username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOtherUsersCards($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM cards_users WHERE user_name != ?");
        $stmt->execute([$username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCardToUser($cardName, $username, $url) {
        // Vérifier si la carte existe déjà pour l'utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM cards_users WHERE card_name = ? AND user_name = ?");
        $stmt->execute([$cardName, $username]);
        $existingCard = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCard) {
            // Si la carte existe, mettre à jour la quantité
            $stmt = $this->pdo->prepare("UPDATE cards_users SET quantity = quantity + 1 WHERE card_name = ? AND user_name = ?");
            return $stmt->execute([$cardName, $username]);
        } else {
            // Si la carte n'existe pas, l'ajouter
            $stmt = $this->pdo->prepare("INSERT INTO cards_users (card_name, user_name, url, quantity) VALUES (?, ?, ?, 1)");
            return $stmt->execute([$cardName, $username, $url]);
        }
    }

    public function updateCardQuantity($cardName, $username, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE cards_users SET quantity = quantity + ? WHERE card_name = ? AND user_name = ?");
        return $stmt->execute([$quantity, $cardName, $username]);
    }
} 