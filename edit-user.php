<?php

/**
 * @var pdo $db
 * @var mixed $user
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

// si il n'y a pas de parametres dans l'url
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo '{"message:" : "Il manque dans l\'url l\'identifiant de l\'utilisateur à modifier"}';
    exit();
}
// on récupère l'ancien utilisateur dans la base de données
$stmt = $db->prepare("SELECT * FROM user WHERE id=:id");

$stmt->bindValue('id', $_GET['id']);
$stmt->execute();
$userDb = $stmt->fetch();
// si l'utilisateur n'existe pas, on envoie une erreur 404
if (!$userDb) {
    http_response_code(404);
    echo '{"message:" : "L\'utilisateur n\'existe pas"}';
    exit();
}

// si l'utilisateur n'a pas fourni de nouveau mot de passe,
// on affecte l'ancien mot de passe
if ($user->password == '') {
    $user->password = $userDb['password'];
} else {
    // sinon, on hash le nouveau mot de passe fourni
    $user->password =  password_hash($user->password, PASSWORD_DEFAULT);
}


$stmt = $db->prepare("UPDATE user 
                            SET email = :email,
                                password = :password,
                                firstname = :firstname,
                                lastname = :lastname,
                                id_role = :id_role
                            WHERE id = :id");

$stmt->bindValue("email", $user->email);
$stmt->bindValue("password", $user->password);
$stmt->bindValue("firstname", $user->firstname);
$stmt->bindValue("lastname", $user->lastname);
$stmt->bindValue("id_role", $role['id']);
$stmt->bindValue("id", $_GET['id']);


$stmt->execute();

echo '{"message" : "Modification réussie"}';