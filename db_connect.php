<?php

// Connect to MySQL database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'register.test';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

echo "bachka";

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $uname = $_POST["uname"];
  $pword = $_POST["pword"];
  $email = $_POST["email"];

  // Insert data into users table
  $sql = "INSERT INTO users (username, password, email) VALUES ('$uname', '$pword', '$email')";

  if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
}

// Close database connection
mysqli_close($conn);

?>