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

$deleteUserId = $_GET['id'];


$stmt = $db->prepare("DELETE FROM user WHERE id = :id");
$stmt->bindValue('id', $deleteUserId);
$stmt->execute();

echo '{"message" : "L\'utilisateur a bien été supprimé"}';