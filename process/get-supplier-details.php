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
        if (isset($_GET['supID'])) {
            $sup_id = $_GET['supID'];

            $sup_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = '$sup_id'";
            if ($sup_result = $conn->query($sup_sql)) {
                if ($sup_result->num_rows > 0) {
                    $sup = $sup_result->fetch_assoc();
                    $sup_name = $sup['NAME'];
                    $sup_address = $sup['ADDRESS'];
                    $contact_person = $sup['CONTACT_PERSON'];
                    $contact_no = $sup['CONTACT_NO'];

                    $response = [$sup_name, $sup_address, $contact_person, $contact_no];
                    echo json_encode($response);
                } else {
                    echo 'no supplier';
                }
            } else {
                echo 'no supplier';
            }
        } else {
            echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
