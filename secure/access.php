<?php

// Access database

// Declare class to access this php file
class access {

    // Connection global variables
    var $host = null;
    var $user = null;
    var $pass = null;
    var $conn = null;
    var $result = null;

    // Construction class
    function __construct($dbhost, $dbuser, $dbpass, $dbname) {

        $this->host = $dbhost;
        $this->user = $dbuser;
        $this->pass = $dbpass;
        $this->name = $dbname;

    }

    // connection fuction
    public function connect() {

        // Establish connection and store in $conn
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);

        // If error
        if (mysqli_connect_errno()) {
            echo "Could not connect to database";
        }

        // Support all language
        $this->conn->set_charset("utf8");

    }

    // Disconnection function
    public function disconnect() {

        if ($this-> conn != null) {
            $this->conn->close();
        }

    }

    // Insert user details
    public function registerUser($username, $password, $salt, $email, $fullname) {

        // SQL command
        $sql = "INSERT INTO users SET username=?, password=?, salt=?, email=?, fullname=?";
        
        // Store query result in $statement
        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Bind 5 param of type string to be placed in $sql p.s. s represent string
        $statement->bind_param("sssss", $username, $password, $salt, $email, $fullname);

        $returnValue = $statement->execute();

        return $returnValue;


    }

    // Select user information
    public function selectUser($username) {

        // SQL command
        $sql = "SELECT * FROM users WHERE username='".$username."'";
        // Assign result we got from $sql to $result var
        $result = $this->conn->query($sql);

        // If we have at least 1 result returned
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            
            // Assign results we got to $row as associative array
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) {
                $returnArray = $row;
            }
        }

        return $returnArray;

    }

    // Save email confirmation message's token
    public function saveToken($table, $id, $token) {

        $sql = "INSERT INTO $table SET id=?, token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            throw new Expection($statement->error);
        }

        // Bind prarmeters to sql statement
        $statement->bind_param("is", $id, $token);

        // Lanuch / execute and store feedback in $returnValue
        $returnValue = $statement->execute();

        return $returnValue;
        

    }

    // Get ID of user via emailToken he received via email's $_GET
    function getUserID($table, $token) {

        $returnArray =  array();

        // SQL statement
        $sql = "SELECT id FROM $table WHERE token = '".$token."'";
        
        // Lanuch sql statement
        $result = $this->conn->query($sql);

        // If $result is not empty and storing some content
        if ($result != null && (mysqli_num_rows($result) >= 1 )) {
            
            // Content from $result convert to cassoc array and store in $row
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) {
                $returnArray = $row;
            }

        }

        return $returnArray;

    }

    // Change status of emailConfirmation column
    function emailConfirmationStatus($status, $id) {

        $sql = "UPDATE users SET emailConfirmed=? WHERE id=?";
        $statement = $this->conn->prepare($sql);
        
        if (!$statement) {
            throw new Expection($statement->error);
        }

        $statement->bind_param("ii", $status, $id);
        $returnValue = $statement->execute();
        return $returnValue;

    }

    // Delete token once email is confirmed
    function deleteToken($table, $token) {

        $sql = "DELETE FROM $table WHERE token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            throw new Exception($statement->error);
        }

        $statement->bind_param("s", $token);
        $returnValue = $statement->execute();
        return $returnValue;

    }

    // Get full user information
    public function getUser($username) {
        // Declare array to store all information we got
        $returnArray = array();
        
        // SQL statement
        $sql = "SELECT * FROM users WHERE username='".$username."'";

        // Excute / query $sql
        $result = $this->conn->query($sql);

        // If we got some result
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
        }

        // If assigned to $row. Assign everything $returnArray
        if (!empty($row)) {
            $returnArray = $row;
        }

        return $returnArray;

    }

}

?>