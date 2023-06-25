<?php 
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestMethod == "POST")
{
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->transaction_id) && isset($data->rider_id) && isset($data->payment)){
        $transaction_id = $data->transaction_id;
        $rider_id = $data->rider_id;
        $payment = $data->payment;

        $scanQR = scanQR($transaction_id, $rider_id, $payment);
        echo $scanQR;
    } else {
        $data = [
            'status' => 405,
            'message' => 'Access Denied',
        ];
        header("HTTP/1.0 405 error");
        echo json_encode($data);
    }
}
else
{
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>