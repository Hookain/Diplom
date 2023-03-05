<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    include 'db_connect.php';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password";
        }
    } else {
        $error_message = "Username not found";
    }

    $stmt->close();
    $conn->close();
}
?>
