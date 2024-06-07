<?php

$headers = apache_request_headers();

if (!isset($headers['Authorization'])) {
    http_response_code(403);
    echo '{"message" : "vous n\'etes pas connecté"}';
    exit();
}

$jwt = $headers['Authorization'];

$jwtParts = explode('.', $jwt);

$enTete = $jwtParts[0];
$corp = $jwtParts[1];
$signature = $jwtParts[2];

$signatureRecalcule = hash_hmac('sha256', "$enTete.$corp", 'votre_cle_secrete', true);
$signatureRecalculeBase64 = rtrim(strtr(base64_encode($signatureRecalcule), '+/', '-_'), '=');

if ($signature !== $signatureRecalculeBase64) {
    http_response_code(403);
    echo '{"message":"signature invalide"}';
    exit;
}

$corpBase64 = str_replace(['-', '_'], ['+', '/'], $corp);
$jsonUser = base64_decode($corpBase64);
$user = json_decode($jsonUser);
