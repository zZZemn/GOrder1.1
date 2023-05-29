<?php 
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Address-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestMethod == "POST")
{
    $data = json_decode(file_get_contents("php://input"));

    $cust_id = $data->cust_id;
    $payment_type = $data->payment_type;
    $delivery_type = $data->delivery_type;
    $unit_st = $data->unit_st;
    $bgy_id = $data->bgy_id;

    $check_out = checkOut($cust_id, $payment_type, $delivery_type, $unit_st, $bgy_id);
    echo $check_out;
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