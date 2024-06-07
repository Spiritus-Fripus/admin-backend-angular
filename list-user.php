<?php
/**
 * @var pdo $db
 * @var object $user
 * */

include('header-init.php');
include ('extract-jwt.php');

if ($user->role != 'Admin' && $user->role != 'Teacher'){
    echo '{"message": "Vous n\'avez pas les droits nécessaires';
    http_response_code(403);
    exit();
}

$stmt = $db->query('SELECT u.id , u.email, u.firstname , u.lastname, r.name AS role FROM user AS u JOIN role AS r ON u.id_role =r.id');


$users = $stmt->fetchAll();

echo json_encode($users);