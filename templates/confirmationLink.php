<?php

$token = htmlentities($_GET["token"]);

// STEP.1 Check required $ passend information
if (empty($token)) {
    echo "Missing required information";
}

// STEP.2 Build connection
// Secure way to build connection
$file = parse_ini_file("../../../../Twitter.ini");

// Get info form Twitter.ini
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

// Include access.php to call func from it
require ("../secure/access.php");
$access = new access($host, $user, $pass, $name);
$access->connect();

// STEP.3 Get id of user (write in access.php)
// Store in $id the result of func
$id = $access->getUserID("emailTokens", $token);

if (empty($id["id"])) {
    echo "User with this token not found";
    return;
}

// STEP.4 Change status of confirmation and delete token
$result = $access->emailConfirmationStatus(1, $id["id"]);

if ($result) {
    // STEP.4-1 Delete token from 'emailTokens'
    $access->deleteToken("emailTokens", $token);
    echo "Thank you! Your email is now confirmed";
}

// STEP.5 Close connection
$access->disconnect();

?>