<?php
include('db_connect.php');
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: index.php');
  exit;
}

// Prepare and execute SQL statement
$stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

// Fetch account information from the database
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $username = $row['username'];
  $email = $row['email'];
} else {
  $username = "";
  $email = "";
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./styles.css" />
  <title>SSL Website</title>

  <style>
    body {
      background-image: url("./images/LED-Street-Light.jpg");
      background-repeat: no-repeat;
      background-size: cover;
    }
  </style>

</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="my_lamps.php">My Lamps</a>
      <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              My Account
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="dashboard.php">Go Back</a>
              <a class="dropdown-item" href="index.php">Sign Out</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="col-md-6 offset-md-3">
  <section>
    <h2>Account Information</h2>
    <form>
      <?php if (!empty($username) && !empty($email)): ?>
        <article>
          <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly>
          </div>
          <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
          </div>
        </article>
      <?php else: ?>
        <div class="error">No account information found.</div>
      <?php endif; ?>
    </form>
  </section>
</div>


  <div class="container mt-4">
    <div class="row">
      <div class="col-md-6 offset-md-3">

      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
