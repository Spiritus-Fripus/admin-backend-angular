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

$stmt = $db->query("SELECT u.id, u.firstname, u.lastname, d.justify,d.date
         FROM user AS u 
         JOIN delay AS d 
         ON u.id = d.id_latecomer ");

$latecomer = $stmt->fetchAll();

echo json_encode($latecomer);
