<?php 
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Content-Type, Address-Control-Allow-Headers, Autorization, X-Request-With');

include('functions.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestMethod == "POST")
{
    $data = json_decode(file_get_contents("php://input"));

    $fname = $data->fname;
    $lname = $data->lname;
    $mi = $data->mi;
    $suffix = $data->suffix;
    $sex = $data->sex;
    $email = $data->email;
    $username = $data->username;
    $password = $data->password;
    $contact_no = $data->contact_no;
    $unit_street = $data->unit_street;
    $barangay = $data->barangay;
    $municipality = $data->municipality;
    $province = $data->province;
    $region = $data->region;
    $birthday = $data->birthday;

    $login_status = signup($fname, $lname, $mi, $suffix, $sex, $email, $username, $password, $contact_no, $unit_street, $barangay, $municipality, $province, $region, $birthday);
    echo $login_status;
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