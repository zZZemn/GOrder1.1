<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
        $product_id = $_GET['product_id'];
        $qty = $_GET['quantity'];

        $inv_sql = "SELECT SUM(QUANTITY) as total_quantity FROM `inventory` WHERE `PRODUCT_ID` = '$product_id'";
        if ($inv_result = $conn->query($inv_sql)) {
            if ($inv_result->num_rows > 0) {
                $inv = $inv_result->fetch_assoc();
                $inv_qty = $inv['total_quantity'];

                // echo $inv_qty.' - '.$qty;

                if ($qty <= $inv_qty) {
                    echo true;
                } else {
                    echo 'here';
                }
            } else {
                echo 'here 1';
            }
        } else {
            echo 'here 2';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
