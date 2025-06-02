<?php
class Friendship {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getFriends($userId) {
        $stmt = $this->pdo->prepare("
            SELECT iduser, username 
            FROM user 
            WHERE iduser != :me
        ");
        $stmt->execute(['me' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingInvites($userId) {
        $stmt = $this->pdo->prepare("
            SELECT receiver_id, status
            FROM friendships
            WHERE sender_id = :sender_id AND status = 0
        ");
        $stmt->execute(['sender_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getReceivedInvites($userId) {
        $stmt = $this->pdo->prepare("
            SELECT sender_id, status
            FROM friendships
            WHERE receiver_id = :receiver_id AND status = 0
        ");
        $stmt->execute(['receiver_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAcceptedFriends($userId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                CASE 
                    WHEN sender_id = :me THEN receiver_id
                    ELSE sender_id
                END as friend_id
            FROM friendships 
            WHERE (sender_id = :me OR receiver_id = :me)
                AND status = 1
        ");
        $stmt->execute(['me' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} 