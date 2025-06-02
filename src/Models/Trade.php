<?php
class Trade {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createTrade($senderName, $receiverName, $senderCards = [], $receiverCards = []) {
        $this->pdo->beginTransaction();
        try {
            // Créer le trade
            $stmt = $this->pdo->prepare("INSERT INTO trades (sender_name, receiver_name, status) VALUES (?, ?, 0)");
            $stmt->execute([$senderName, $receiverName]);
            $tradeId = $this->pdo->lastInsertId();

            // Ajouter les cartes du sender
            if (!empty($senderCards)) {
                $this->addCardsToTrade($tradeId, $senderCards, true);
            }

            // Ajouter les cartes du receiver
            if (!empty($receiverCards)) {
                $this->addCardsToTrade($tradeId, $receiverCards, false);
            }

            $this->pdo->commit();
            return $tradeId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function addCardsToTrade($tradeId, $cards, $isSender = true) {
        $quantityField = $isSender ? 'card_quantity_sender' : 'card_quantity_friend';
        $stmt = $this->pdo->prepare("INSERT INTO trading_cards (trade_id, card_name, $quantityField) VALUES (?, ?, ?)");
        
        foreach ($cards as $card) {
            $stmt->execute([$tradeId, $card['name'], $card['quantity']]);
        }
    }

    public function getPendingTrades($username) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM trades 
            WHERE (sender_name = ? OR receiver_name = ?) 
            AND status = 0
        ");
        $stmt->execute([$username, $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTradeCards($tradeId) {
        $stmt = $this->pdo->prepare("
            SELECT card_name, card_quantity_sender, card_quantity_friend 
            FROM trading_cards 
            WHERE trade_id = ?
        ");
        $stmt->execute([$tradeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function acceptTrade($tradeId) {
        $this->pdo->beginTransaction();
        try {
            // Récupérer les informations du trade
            $stmt = $this->pdo->prepare("SELECT sender_name, receiver_name FROM trades WHERE id = ?");
            $stmt->execute([$tradeId]);
            $trade = $stmt->fetch(PDO::FETCH_ASSOC);

            // Récupérer les cartes du trade
            $cards = $this->getTradeCards($tradeId);

            // Échanger les cartes
            foreach ($cards as $card) {
                if ($card['card_quantity_sender'] > 0) {
                    $this->exchangeCards(
                        $trade['sender_name'],
                        $trade['receiver_name'],
                        $card['card_name'],
                        $card['card_quantity_sender']
                    );
                }
                if ($card['card_quantity_friend'] > 0) {
                    $this->exchangeCards(
                        $trade['receiver_name'],
                        $trade['sender_name'],
                        $card['card_name'],
                        $card['card_quantity_friend']
                    );
                }
            }

            // Mettre à jour le statut du trade
            $stmt = $this->pdo->prepare("UPDATE trades SET status = 1 WHERE id = ?");
            $stmt->execute([$tradeId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function exchangeCards($fromUser, $toUser, $cardName, $quantity) {
        // Retirer les cartes de l'expéditeur
        $stmt = $this->pdo->prepare("
            UPDATE cards_users 
            SET quantity = quantity - ? 
            WHERE user_name = ? AND card_name = ?
        ");
        $stmt->execute([$quantity, $fromUser, $cardName]);

        // Ajouter les cartes au destinataire
        $stmt = $this->pdo->prepare("
            INSERT INTO cards_users (user_name, card_name, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ");
        $stmt->execute([$toUser, $cardName, $quantity, $quantity]);
    }

    public function cancelTrade($tradeId) {
        $this->pdo->beginTransaction();
        try {
            // Supprimer les cartes associées au trade
            $stmt = $this->pdo->prepare("DELETE FROM trading_cards WHERE trade_id = ?");
            $stmt->execute([$tradeId]);

            // Supprimer le trade
            $stmt = $this->pdo->prepare("DELETE FROM trades WHERE id = ?");
            $stmt->execute([$tradeId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
} 