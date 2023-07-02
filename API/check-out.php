<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Content-Type, Address-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id) && isset($data->payment_type) && isset($data->delivery_type)) {
        $id = $data->id;
        $payment_type = $data->payment_type;
        $delivery_type = $data->delivery_type;
        $order = checkout($id, $payment_type, $delivery_type);
        echo $order;
    } else {
        $data = [
            'status' => 405,
            'message' => 'No id found',
        ];
        header("HTTP/1.0 405 Method Not Allowed");
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
