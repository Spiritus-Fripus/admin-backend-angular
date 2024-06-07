<?php
/** @var pdo $db */

include('header-init.php');

$json = file_get_contents('php://input');

$user = json_decode($json);

$sql = $db->prepare(
    " SELECT u.id,u.email,u.password, u.firstname, u.lastname, r.name AS role
            FROM user AS u 
            JOIN role AS r ON u.id_role = r.id
            WHERE u.email = :email
           ");

$sql->bindValue('email', $user->email);
$sql->execute();

$userDb = $sql->fetch();

// si user n'existe pas
if (!$userDb || !password_verify($user->password, $userDb['password'])) {
    echo '{"message" : "login ou password incorrect"}';
    http_response_code(403);
    exit();
}

function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

$payload = json_encode([
    'id' => $userDb['id'],
    'role' => $userDb['role'],
    'email' => $userDb['email'],
    'firstname' => $userDb['firstname'],
    'lastname' => $userDb['lastname']
]);


// Encoder en Base64 URL-safe
$base64UrlHeader = base64UrlEncode($header);
$base64UrlPayload = base64UrlEncode($payload);

// Créer la signature
$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'votre_cle_secrete', true);
$base64UrlSignature = base64UrlEncode($signature);

// Assembler le token
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

echo '{"jwt" : "' . $jwt . '"}';
