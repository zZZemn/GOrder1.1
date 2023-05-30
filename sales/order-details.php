<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();


    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['transaction_id'])) {
            $transactionID = filter_input(INPUT_GET, 'transaction_id', FILTER_SANITIZE_STRING);
            $orderDetails_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transactionID'";
            $orderDetails_result = $conn->query($orderDetails_sql);
            if ($orderDetails_result->num_rows > 0) {
                $order = $orderDetails_result->fetch_assoc();

?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
                    </style>
                    <link rel="stylesheet" href="../css/order-details.css">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
                    <title>GOrder | Order Details</title>
                </head>

                <body>

                    <div class="top-contents-container">
                        <p class="transaction-id"><?php echo $order['TRANSACTION_ID'] ?></p>
                        <div class="status-container">
                            <div class="progress" style="height: 8px; width: 400px">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-circle">
                                <div class="circle bg-primary" style=" left: 48%;"><span class="bg-primary">value</span></div>
                            </div>
                            <div class="progress-photo-container">
                                <i class="fa-solid fa-location-dot" id="waiting"></i>
                                <i class="fa-solid fa-check" id="accepted"></i>
                                <i class="fa-solid fa-box-open" id="for-delivery"></i>
                                <i class="fa-solid fa-motorcycle" id="shipped"></i>
                                <i class="fa-solid fa-house-circle-check" id="delivered"></i>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
                </body>
<?php

            } else {
                echo "
                    <head>
                        <link rel='stylesheet' href='../css/access-denied.css'>
                    </head>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Transaction ID not found.</h5>
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
