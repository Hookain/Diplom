<?php

// Set up database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dr";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
