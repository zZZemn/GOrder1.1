<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include('functions.php');
include('../database/db.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "POST") {
    if (isset($_POST['user_id'], $_FILES['valid_id'])) {
        $uploadValidId = uploadValidId($_POST['user_id'], $_FILES['valid_id']);
        echo $uploadValidId;
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
