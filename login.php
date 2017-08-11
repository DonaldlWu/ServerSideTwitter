<?php

// STEP.1 Check variables passing to this gile via POST
$username = htmlentities($_REQUEST["username"]);
$password = htmlentities($_REQUEST["password"]);

if (empty($username) || empty($password)) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    echo json_encode($returnArray);
    return;
}

// STEP.2 Build connection
// Secure way to build connection
$file = parse_ini_file("../../../Twitter.ini");

// Get info form Twitter.ini
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

// Include access.php to call func from it
require ("secure/access.php");
$access = new access($host, $user, $pass, $name);
$access->connect();

// STEP.3 Get user inf
// Assign result of excution of getUser to $user var
$user = $access->getUser($username);

// If we not get any user's inf
if (empty($user)) {
    $returnArray["status"] = "403";
    $returnArray["message"] = "User not found";
    echo json_encode($returnArray);
    return;
}

// STEP.4 Check validity of entered password
// Get password and salt from db
$secured_password = $user["password"];
$salt = $user["salt"];

// Check do passwords match: from db & entered one
if ($secured_password == sha1($password . $salt)) {

    $returnArray["status"] = "200";
    $returnArray["message"] = "Logged in successfully";
    $returnArray["id"] = $user["id"];
    $returnArray["username"] = $user["username"];
    $returnArray["email"] = $user["email"];
    $returnArray["fullname"] = $user["fullname"];
    $returnArray["ava"] = $user["ava"];

} else {

    $returnArray["status"] = "403";
    $returnArray["message"] = "Passwords do not match";

}

// STEP.5 Close connection
$access->disconnect();

// STEP.6 Throw back all information to user
echo json_encode($returnArray); 

?>