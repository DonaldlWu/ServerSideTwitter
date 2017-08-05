<?php


class email {

    // Generate unique token for user when he got confirmation email message
    function generateToken($length) {

        // Some characters
        $character = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";

        // Get length of characters string
        $characterLength = strlen($character);

        $token = '';

        // generate random char from $character every time until it is less than $characterLength
        for ($i = 0; $i <$length; $i++) {
            $token .= $character[rand(0, $characterLength-1)];
        }
        return $token;

    }

    // Open confirmation template user gonna receive
    function confirmationTemplate() {

        // Open file
        $file = fopen("templates/confirmationTemplate.html", "r") or die("Unable to open file");
    
        // Store content of file in $template var
        $template = fread($file, filesize("templates/confirmationTemplate.html"));
    
        fclose($file);
        return $template;

    }

    // Senf email via php
    function sendEmail($details) {

        // Information of email
        $subject = $details["subject"];
        $to = $details["to"];
        $fromName = $details["fromName"];
        $fromEmail = $details["fromEmail"];
        $body = $details["body"];

        // Header required by some of smtp or mail sites
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;content=UTF-8" . "\r\n";
        $headers .= "From: " . $fromName . " <" . $fromEmail . ">" . "\r\n"; // From yohoho wu <donaldwu1101@gmail.com>

        // PHP func to send email finally
        mail($to, $subject, $body, $headers);       

    }

}


?>