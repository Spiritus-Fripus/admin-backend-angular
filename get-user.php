<?php
/**
 * @var pdo $db
 * @var object $user
 * */

include('header-init.php');
include ('extract-jwt.php');

if ($user->role != 'Admin'){
    echo '{"message": "Vous n\'avez pas les droits nécessaires';
    http_response_code(403);
    exit();
}


if(!isset($_GET['id'])) {
    http_response_code(400);
    echo '{"message" : "il manque l\'identifiant dans l\'url"}';
    exit();
}

$idUser = $_GET['id'];

$stmt = $db->prepare('
    SELECT u.id, u.email, u.firstname, u.lastname, r.name AS role
    FROM user AS u
    JOIN role AS r ON u.id_role = r.id
    WHERE u.id = :id'
);

$stmt->bindValue('id', $idUser);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo '{"message" : "Utilisateur introuvable"}';
    exit();
}

echo json_encode($user);