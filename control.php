<?php
    include_once('config.php');

    $status = $_POST['status'] == 'true' ? false : true;
    header('Content-Type: application/json; charset=utf-8');
    $data = [];    

    $postdata = new stdClass();
    $postdata->method = "setGpioStatus";
    $postdata->params = new stdClass();
    $postdata->params->pin = 0;
    $postdata->params->enabled = $status;
    
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
    
    $resultData = json_decode($result);
    if ($resultData === null) {
        echo "Error parsing response from ThingsBoard.";
        exit;
    }

    $data['success'] = true;
    $data['result'] = $resultData;
    
    echo json_encode($data);    
    
?>