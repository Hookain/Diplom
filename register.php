<?php
include('db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@1,300&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="./login.css" />
</head>
<body>
     <section>
        <article>
            <form action="check_redirect.php" method="post" novalidate>
                <div>
                    <h1>Create new account</h1>
                </div>
    
                <div>
                    <label for="username">Username<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" pattern="[a-zA-Z0-9!@#$%^&*()_+-=,.<>/?;:'\"[\]{}|`~]+" required> 
                </div>

    
                <div>
                    <label for="password">Password<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" pattern="^(?=.*[A-Za-z])[A-Za-z\d]{8,}$" required>  
                </div>
                
                <div>
                    <label for="cpassword">Confirm Password<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" pattern="^(?=.*[A-Za-z])[A-Za-z\d]{8,}$" required> 
                </div>

                <div>
                    <label for="email">Email<span style="color: red; font-size: 20px;">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required>
                </div>
                
                <div class="buttons">
                    <button type="submit" class="button" id="register">Create</button>
                </div>
            </form>
            <form action="login.php" method="GET">
                <div class="buttons">
                    <button type="submit" class="button" id="login">Already have account?</button>
                </div>
            </form>
            <script>
                // Password validation
                const passwordField = document.getElementById('password');
                const passwordPopup = document.createElement('div');
                passwordPopup.textContent = 'Password must be at least 8 characters long, and must contain at least one letter.';
                passwordPopup.style.color = 'red';
                passwordPopup.style.display = 'none';
                passwordField.parentNode.insertBefore(passwordPopup, passwordField.nextSibling);
                passwordField.addEventListener('invalid', () => {
                    if (passwordField.validity.patternMismatch) {
                    passwordPopup.style.display = 'block';
                    } else {
                    passwordPopup.style.display = 'none';
                    }
                });

                // Confirm password validation
                const confirmPasswordField = document.getElementById('cpassword');
                const confirmPasswordPopup = document.createElement('div');
                confirmPasswordPopup.textContent = 'Passwords do not match.';
                confirmPasswordPopup.style.color = 'red';
                confirmPasswordPopup.style.display = 'none';
                confirmPasswordField.parentNode.insertBefore(confirmPasswordPopup, confirmPasswordField.nextSibling);
                confirmPasswordField.addEventListener('input', () => {
                    if (confirmPasswordField.value !== passwordField.value) {
                    confirmPasswordPopup.style.display = 'block';
                    } else {
                    confirmPasswordPopup.style.display = 'none';
                    }
                });

                // Email validation
                const emailField = document.getElementById('email');
                const emailPopup = document.createElement('div');
                emailPopup.textContent = 'Please enter a valid email address.';
                emailPopup.style.color = 'red';
                emailPopup.style.display = 'none';
                emailField.parentNode.insertBefore(emailPopup, emailField.nextSibling);
                emailField.addEventListener('input', () => {
                const email = emailField.value.trim();
                const isValidFormat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                if (!isValidFormat) {
                    emailPopup.style.display = 'block';
                } else {
                    emailPopup.style.display = 'none';
                }
                });
            </script>
        </article>
    </section>
</body>
</html>
