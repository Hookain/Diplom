<?php

include_once('config.php');

/*
// Retrieve devices from ThingsBoard using the access token
$options = array(
    'http' => array(
        'header' => "X-Authorization: Bearer ".$_SESSION['token']."\r\n",
        'method' => 'GET'
    )
);

$context = stream_context_create($options);
$result = file_get_contents($devicesUrl, false, $context);

if ($result === false) {
    echo "Failed to retrieve data from ThingsBoard.";
    exit;
}

$data = json_decode($result);
if ($data === null) {
    echo "Error parsing response from ThingsBoard.";
    exit;
}


// Retrieve keys from ThingsBoard using the access token
$result = file_get_contents($keysUrl, false, $context);

if ($result === false) {
    echo "Failed to retrieve data from ThingsBoard.";
    exit;
}

$keys = json_decode($result);
if ($keys === null) {
    echo "Error parsing response from ThingsBoard.";
    exit;
}

// Retrieve values from ThingsBoard using the access token
$result = file_get_contents($valuesUrl, false, $context);

if ($result === false) {
    echo "Failed to retrieve data from ThingsBoard.";
    exit;
}

$values = json_decode($result);
if ($values === null) {
    echo "Error parsing response from ThingsBoard.";
    exit;
}
*/

// Retrieve relay status from ThingsBoard using the access token

$postdata = new stdClass();
$postdata->method = "getStatus";
$postdata->params = new stdClass();

$postdata = json_encode($postdata);

$options = array(
    'http' => array(
        'method'  => "POST",
        'header'  => "Content-type: application/json\r\n"."X-Authorization: Bearer ".$_SESSION['token']."\r\n",
        'content' => $postdata,
        'ignore_errors' => true
    )
);

$context = stream_context_create($options);
$result = file_get_contents($controlUrl, false, $context);

if ($result === false) {
    echo "Failed to retrieve data from ThingsBoard.";
    exit;
}   

$status = json_decode($result);
if ($status === null) {
    echo "Error parsing response from ThingsBoard.";
    exit;
}

$status = json_decode($status);

?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="./my_lamps.css" />  

  <title>SmartLights Website</title>

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
        <button class="lamp-info__toggle <?=$status->{0} ? 'on' : 'off'?>"><?=$status->{0} ? 'On' : 'Off'?></button>
      </div>
    </div>
  </article>
</section>
<section>
  <article>
      <div>        
        T: <span><?=$status->t?></span>
      </div>
      <div>        
        H: <span><?=$status->H?></span>
      </div>
      <div>        
        W: <span><?=$status->W?></span>
      </div>

    </article>
</section>

<script>
const toggleBtn = document.querySelector('.lamp-info__toggle');
let isOn = <?=$status->{0} ? 1 : 0?>;
toggleBtn.addEventListener('click', function() {
  if (isOn) {
    // Turn off
    $.post( "control.php", {status: false}, function( data ) {
      if(data.success){
        toggleBtn.textContent = 'Off';
        toggleBtn.classList.remove('on');
        toggleBtn.classList.add('off');
        isOn = false;
      }
    });    
  } else {
    // Turn on
    $.post( "control.php", {status: true}, function( data ) {
      if(data.success){
        toggleBtn.textContent = 'On';
        toggleBtn.classList.remove('off');
        toggleBtn.classList.add('on');
        isOn = true;
      }
    });    
  }
});
</script>


</body>
</html>
