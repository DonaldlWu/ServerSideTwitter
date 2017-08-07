<?php

// STEP.1 Declare parms of user info

// Securing information and storing variables
$username = htmlentities($_REQUEST["username"]);
$password = htmlentities($_REQUEST["password"]);
$email = htmlentities($_REQUEST["email"]);
$fullname = htmlentities($_REQUEST["fullname"]);

// If GET or POST are empty 
if (empty($username) || empty($password) || empty($email) || empty($fullname)) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing require information";
    echo json_encode($returnArray);
    return;
}

// Scure password
$salt = openssl_random_pseudo_bytes(20);
$scured_password = sha1($password . $salt);

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

// STEP.3 Insert user information
$result = $access->registerUser($username, $scured_password, $salt, $email, $fullname);

// Successfully registered
if ($result) {

    // Got current registered user information and store in $user
    $user = $access->selectUser($username);

    // Declare information to feedback to user of app as json
    $returnArray["status"] = "200";
    $returnArray["message"] = "Successfully registered";
    $returnArray["id"] = $user["id"];
    $returnArray["username"] = $user["username"];
    $returnArray["fullname"] = $user["fullname"];
    $returnArray["ava"] = $user["ava"];


    // STEP.4 Emailing
    // Include email.php
    require ("secure/email.php");

    // Store all class in $email var
    $email = new email();

    // Store generated token in $toke var
    $token = $email->generateToken(20);
    // Save information in emailTokens table 

    $access->saveToken("emailTokens", $user["id"], $token);

    // Append emailing information
    $details = array();
    $details["subject"] = "Email confirmation on Twitter";
    $details["to"] = $user["email"];
    $details["fromName"] = "YohohoWu";
    $details["fromEmail"] = "donaldwu1101@gmail.com";

    // Access template file
    $template = $email->confirmationTemplate();

    // Replace {token} from confirmationTemplate.html by $token and store all content in $template var 
    $template = str_replace("{token}", $token, $template);

    $details["body"] = $template;
    $email->sendEmail($details);

} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not register with provided information";
}

// STEP.5 Close connection
$access->disconnect();

// STEP.6 Json data
echo json_encode($returnArray);

?>