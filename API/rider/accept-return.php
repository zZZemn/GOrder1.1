<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $riderID = $data->rider_id;
    $returnID = $data->return_id;

    $return = acceptReturn($riderID, $returnID);
    echo $return;
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
