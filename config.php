<?php
    $baseUrl = 'http://zaimov.eu:8181';
    $device_id = '8014eeb0-7c65-11ed-89e5-8f9e9abd2970'; // Replace with your device ID
    $devicesUrl = $baseUrl.'/api/tenant/devices?pageSize=100&page=0';
    $keysUrl = $baseUrl."/api/plugins/telemetry/DEVICE/{$device_id}/keys/timeseries";
    $valuesUrl = $baseUrl."/api/plugins/telemetry/DEVICE/{$device_id}/values/timeseries?keys=t,h,W";
    $controlUrl = $baseUrl."/api/plugins/rpc/twoway/{$device_id}";
    
    session_start();

    //if(!isset($_SESSION['token'])){
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
        $result = file_get_contents($baseUrl.'/api/auth/login', false, $context);
      
        if ($result === false) {
            echo "Failed to log in.";
            exit;
        }
      
        $token = json_decode($result)->token;
        $_SESSION['token'] = $token;
    //}
      
?>