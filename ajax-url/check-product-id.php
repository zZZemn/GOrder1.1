<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $check_id_sql = $conn->query("SELECT * FROM `products` WHERE `PRODUCT_ID` = '$id'");
        if ($check_id_sql->num_rows > 0) {
            echo true;
        } else {
            echo false;
        }
    } else {
        echo false;
    }
} else {
    header("Location: ../index.php");
    exit;
}
