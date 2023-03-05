<?php 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    include "db_connect.php";

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@1,300&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="./login.css" />
</head>
<body>
    <section>
        <article>
            <form action="" method="post" id="login-form">
                <div>
                    <h1>LogIn</h1>
                </div>
    
                <div>
                    <label for="username">Username<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" pattern="" required>
                </div>
    
                <div>
                    <label for="password">Password<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" pattern="[a,z], [0,9]" required>  
                </div>
                <div class="buttons">
                    <button type="submit" class="button" id="login">Log In</button>
                </div>
            </form>
            <form action="register.php" method="GET">
                <!-- <div class="buttons">
                    <button type="submit" class="button" id="register">Create new account</button>
                </div> -->
            </form>
        </article>
    </section>
</body>
</html>
