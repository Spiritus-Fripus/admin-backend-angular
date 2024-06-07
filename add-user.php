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

$json = file_get_contents('php://input');


$user = json_decode($json);


$stmt = $db->prepare("SELECT id FROM role WHERE name = :name");

$stmt->bindValue("name", $user->role);
$stmt->execute();
$role = $stmt->fetch();

if (!$role) {
    http_response_code(400);
    echo('{"message" : "Ce role n\'existe pas"}');
    exit();
}


$passwordHash = password_hash($user->password, PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO user (email, password,firstname,lastname,id_role) VALUES (:email, :password,:firstname, :lastname, :id_role)");


$stmt->bindValue("email", $user->email);
$stmt->bindValue("password", $passwordHash);
$stmt->bindValue("firstname", $user->firstname);
$stmt->bindValue("lastname", $user->lastname);
$stmt->bindValue("id_role", $role['id']);


$stmt->execute();

echo '{"message" : "inscription réussie"}';