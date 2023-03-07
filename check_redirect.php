<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $uname = $_POST["username"];
  $pword = password_hash($_POST["password"], PASSWORD_BCRYPT); // hash the password
  $email = $_POST["email"];

  $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $uname, $pword, $email);

  if ($stmt->execute()) {
    session_start();
    $_SESSION["registration_success"] = true; // add session variable
    header("Location: login.php");
  } else {
      echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];
  
  // Sanitize the inputs to prevent SQL injection attacks
  $username = mysqli_real_escape_string($conn, $username);
  $password = mysqli_real_escape_string($conn, $password);

  $sql = "SELECT * FROM users WHERE username='$username'";
  $result = mysqli_query($conn, $sql);
  
  // if (mysqli_num_rows($result) == 1) {
  //     $row = mysqli_fetch_assoc($result);
  //     $hashed_password = $row['password'];
  //     if (password_verify($password, $hashed_password)) {
  //         // User exists, set session variables and redirect to dashboard
  //         session_start();
  //         $_SESSION["username"] = $username;
  //         header("Location: dashboard.php");
  //         exit();
  //     } else {
  //         // Invalid login credentials, show error message
  //         $error_message = "Invalid login credentials. Please try again.";
  //     }
  // } else {
  //     // Invalid login credentials, show error message
  //     $error_message = "Invalid login credentials. Please try again.";
  // }
}
?>
