<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['id'])) {
        $emp_id = $emp['EMP_ID'];
        $discount_id = $_POST['id'];
        include('../time-date.php');
        $addDate = $currentDate;
        $addTime = $currentTime;

        $update_discount_rate_sql = "UPDATE `discount` SET `DISCOUNT_STATUS`= 'active' WHERE DISCOUNT_ID = '$discount_id'";

        $edit_discount_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Enable(".$discount_id.")','$addDate','$addTime')";

        if ($conn->query($update_discount_rate_sql) === TRUE && $conn->query($edit_discount_log) === TRUE) {
            echo 'edited';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
} else {
    header("Location: ../index.php");
    exit;
}
