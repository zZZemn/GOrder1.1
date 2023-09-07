<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
    if ($emp['EMP_TYPE'] === 'Admin' && $emp['EMP_STATUS'] === 'active') {
        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
            $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
            $product_result = $conn->query($product_sql);
            if($product_result->num_rows > 0){
                $product = $product_result->fetch_assoc();

                $product_name = $product['PRODUCT_NAME'].' '.$product['MG'].' '.$product['G'].' '.$product['ML'];
                $response = [$product_name, $product_id];
                echo json_encode($response);
            } else {
                echo 'No Product Found';
            }
        } else {
            echo  <<<HTML
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
    exit;
}
