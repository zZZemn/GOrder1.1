<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Content-Type, Address-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "POST") {
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->id, $data->old_password, $data->new_password)) {
        echo changePassword($data);
    } else {
        $data = [
            'status' => 405,
            'message' => 'Access Denied',
        ];
        header("HTTP/1.0 405 Access Denied");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
