<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $uname = $_POST["username"];
  $pword = password_hash($_POST["password"], PASSWORD_DEFAULT); // hash the password
  $email = $_POST["email"];

  $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $uname, $pword, $email);

  if ($stmt->execute()) {
    header("Location: dashboard.php");
  } else {
      echo "Error: " . $stmt->error;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Sanitize the inputs to prevent SQL injection attacks
    $password = mysqli_real_escape_string($conn, $password);
    $hashed_password = hash('sha256', $password);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$hashed_password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        // User exists, set session variables and redirect to dashboard
        session_start();
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        // Invalid login credentials, show error message
        $error_message = "Invalid login credentials. Please try again.";
    }
}

  $stmt->close();
  $conn->close();
}
?>
