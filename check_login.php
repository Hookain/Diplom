<?php
session_start();
session_destroy();

include "db_connect.php";

if (isset($_POST['uname']) && isset($_POST['password'])){

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['uname']);
    $password = validate($_POST['password']);

    if (empty($uname)){
        header("Location: login.php?error=User Name is required");
        exit();
    }else if(empty($password)){
        header("Location: login.php?error=Password is required");
        exit();
    }else{
        // Sanitize the inputs to prevent SQL injection attacks
        $password = mysqli_real_escape_string($conn, $password);
        $sql = "SELECT * FROM users WHERE username = '$uname'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $stored_password = $row['password'];
            if (password_verify($password, $stored_password)){
                session_start();
                $_SESSION["username"] = $uname;
                header("Location: dashboard.php?success=You have successfully logged in");
                exit();
            } else {
                header("Location: login.php?error=Invalid Password");
                exit();
            }
        } else {
            header("Location: login.php?error=Invalid Username");
            exit();
        }
    }
}else{
    header("Location: login.php");
    exit();
}
?>
