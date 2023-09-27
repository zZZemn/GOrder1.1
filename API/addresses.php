<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: GET');
header('Access-Control-Allow-Headers: Content-Type, Address-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "GET") {
    if (isset($_GET)) {
        if (isset($_GET['region_id'])) {
            // province 
            $provinceResponse = province($_GET['region_id']);
            echo $provinceResponse;
        } elseif (isset($_GET['province_id'])) {
            // municipality
            $municipalityResponse = municipality($_GET['province_id']);
            echo $municipalityResponse;
        } elseif (isset($_GET['municipality_id'])) {
            // barangay
            $barangayResponse = barangay($_GET['municipality_id']);
            echo $barangayResponse;
        } else {
            $regionResponse = regions();
            echo $regionResponse;
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'Access Deny',
        ];
        header("HTTP/1.0 405 Access Deny");
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
