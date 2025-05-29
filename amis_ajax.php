<?php 
require_once("connexion.php");

$userId = $_SESSION['iduser'];
$pending = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajouter une invitation
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['inviteUserId'])) {
        echo json_encode(['success' => false, 'message' => 'Pas d\'ID utilisateur']);
        exit;
    }

    $receiverId = (int)$data['inviteUserId'];

    try {
        // Insère l'invitation
        $sql = "INSERT INTO friendships (sender_id, receiver_id, status) VALUES (:sender_id, :receiver_id, :status)";
        $invite = $pdo->prepare($sql);
        $invite->execute([
            ':sender_id' => $userId,
            ':receiver_id' => $receiverId,
            ':status' => $pending
        ]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
    }
    exit;
}

// GET = récupérer la liste des amis avec statut d'invitation
try {
    // IDs invités en pending
    $stmt = $pdo->prepare("SELECT receiver_id FROM friendships WHERE sender_id = :sender_id AND status = :status");
    $stmt->execute([
        'sender_id' => $userId,
        'status' => $pending
    ]);
    $invitedIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Tous les autres utilisateurs sauf moi
    $stmt2 = $pdo->prepare("SELECT iduser, username FROM user WHERE iduser != :me");
    $stmt2->execute(['me' => $userId]);
    $friends = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Ajout clé 'invited'
    foreach ($friends as &$friend) {
        $friend['invited'] = in_array($friend['iduser'], $invitedIds);
    }

    echo json_encode([
        'success' => true,
        'friends' => $friends
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
}




?>