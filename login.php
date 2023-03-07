<?php
include('db_connect.php');

session_start();

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
            <form action="check_login.php" method="post" id="login-form" novalidate>
                <div>
                    <h1>LogIn</h1>
                </div>

                <?php
                    if (isset($_GET['error'])) { ?>
                    <p vlass="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>
    
                <div>
                    <label for="username">Username<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="text" id="username" name="uname" placeholder="Enter Username" required>
                </div>
    
                <div>
                    <label for="password">Password<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>  
                </div>
                <div class="buttons">
                    <button type="submit" class="button" id="login">Log In</button>
                </div>
            </form>
            <!-- <form action="register.php" method="GET">
                 <div class="buttons">
                    <button type="submit" class="button" id="register">Create new account</button>
                </div>
            </form> -->
        </article>
    </section>
</body>
</html>
