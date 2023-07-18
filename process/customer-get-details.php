<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['cust_id'])) {
            $cust_id = $_POST['cust_id'];
            $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
            if ($cust_result = $conn->query($cust_sql)) {
                if ($cust_result->num_rows > 0) {
                    $cust = $cust_result->fetch_assoc();

                    $cust_details = [
                        'cust_id' => $cust['CUST_ID'],
                        'first_name' => $cust['FIRST_NAME'],
                        'last_name' => $cust['LAST_NAME'], 
                        'middle_initial' => $cust['MIDDLE_INITIAL'],
                        'suffix' => $cust['SUFFIX'],
                        'sex' => $cust['SEX'],
                        'email' => $cust['EMAIL'],
                        'username' => $cust['USERNAME'],
                        'password' => $cust['PASSWORD'],
                        'contact_no' => $cust['CONTACT_NO'],
                        'unit_st' => $cust['UNIT_STREET'],
                        'barangay_id' => $cust['BARANGAY_ID'],
                        'picture' => $cust['PICTURE'],
                        'bday' => $cust['BIRTHDAY'],
                        'discount_type' => $cust['DISCOUNT_TYPE'],
                        'id_pic' => $cust['ID_PICTURE'],
                        'status' => $cust['STATUS']
                    ];

                    echo json_encode($cust_details);
                } else {
                    echo 'not';
                }
            } else {
                echo 'not';
            }
        } else {
            echo "
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>";
        }
    } else {
        echo "
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
