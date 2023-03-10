<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./my_lamps.css" />
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
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              My Account
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="account_information.php">Account Information</a></li>
              <li><a class="dropdown-item" href="index.php">SignOut</a></li>
              <li><hr class="dropdown-divider"></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section>
  <article>
    <div class="lamp-info">
      <div class="lamp-info__name">Lamp</div>
      <div class="lamp-info__status">
        <div class="lamp-info__status-text">Status:</div>
        <button class="lamp-info__toggle on">On</button>
      </div>
    </div>
  </article>
</section>
<section>
  <article>
    <div>
      <label for="humidity">H:</label>
      <input type="text" id="humidity" name="humidity" value="">
    </div>
    <div>
      <label for="temperature">T:</label>
      <input type="text" id="temperature" name="temperature" value="">
    </div>
    <div>
      <label for="wind">W:</label>
      <input type="text" id="wind" name="wind" value="">
    </div>
  </article>
</section>

<script>

<?php

// Login request to obtain access token
$login_data = array(
    'username' => 'viktor.v.apostolov.2018@elsys-bg.org',
    'password' => 'Viktor'
);

$options = array(
    'http' => array(
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($login_data)
    )
);

$context = stream_context_create($options);
$result = file_get_contents('http://zaimov.eu:8181/api/auth/login', false, $context);

if ($result === false) {
    echo "Failed to log in.";
    exit;
}

$token = json_decode($result)->token;

// Retrieve data from ThingsBoard using the access token
$url = 'http://zaimov.eu:8181/api/tenant/devices?pageSize=100&page=0';

$options = array(
    'http' => array(
        'header' => "X-Authorization: Bearer $token\r\n",
        'method' => 'GET'
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "Failed to retrieve data from ThingsBoard.";
    exit;
}

$data = json_decode($result);
if ($data === null) {
    echo "Error parsing response from ThingsBoard.";
    exit;
}

// Display the data on your web server
echo "<table>";
echo "<tr><th>ID</th><th>Name</th></tr>";

foreach ($data->data as $device) {
    $id = $device->id->id;
    $name = $device->name;
    echo "<tr><td>$id</td><td>$name</td></tr>";
}

echo "</table>";

?>



const toggleBtn = document.querySelector('.lamp-info__toggle');
let isOn = true;
toggleBtn.addEventListener('click', function() {
  if (isOn) {
    // Turn off
    toggleBtn.textContent = 'Off';
    toggleBtn.classList.remove('on');
    toggleBtn.classList.add('off');
    isOn = false;
  } else {
    // Turn on
    toggleBtn.textContent = 'On';
    toggleBtn.classList.remove('off');
    toggleBtn.classList.add('on');
    isOn = true;
  }
});
</script>


  <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"></script>

</body>
</html>
