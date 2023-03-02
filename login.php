<?php
    include 'includes/db_connection.php';
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
            <form action="register_check" method="GET">
                <div>
                    <h1>LogIn</h1>
                </div>
    
                <div>
                    <label for="uname">Username<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="text" id="uname" placeholder="Enter Username" pattern="[A,Z], [a,z], [0,9]" required> 
                </div>
    
                <div>
                    <label for="pword">Password<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="password" id="pword" placeholder="Enter Password" pattern="[a,z], [0,9]" required> 
                </div>
                <div class="buttons">
                    <button type="submit" class="button" id="login">Log In</button>
                </div>
            </form>
            <form action="register.php" method="GET">
                <div class="buttons">
                    <button type="submit" class="button" id="register">Create new account</button>
                </div>
            </form>
        </article>
    </section>
</body>
</html>
