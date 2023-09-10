<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['rider_id'], $_POST['return_id'])) {
        $rider_id = $_POST['rider_id'];
        $return_id = $_POST['return_id'];
        $update_sql = "UPDATE `return` SET `RIDER_ID` = '$rider_id' WHERE `RETURN_ID` = '$return_id' AND `STATUS` = 'Pending'";
        if ($conn->query($update_sql)) {
            echo '200';
        } else {
            echo '400';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
