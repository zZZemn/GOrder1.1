<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if(isset($_POST['save_discount_rate'])){
        $emp_id = $emp['EMP_ID'];
        $new_discount_rate = $_POST['discount_rate'];
        include('../time-date.php');
        $addDate = $currentDate;
        $addTime = $currentTime;

        $update_discount_rate_sql = "UPDATE `discount` SET `DISCOUNT_PERCENTAGE`='$new_discount_rate' WHERE 1";

        $edit_discount_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Update discount rate to $new_discount_rate.','$addDate','$addTime')";

        if($conn->query($update_discount_rate_sql) === TRUE && $conn->query($edit_discount_log) === TRUE) {
            header("Location: ../admin/maintenance-discount.php?status=edited");
            exit();
        }
        else {
            header("Location: ../admin/maintenance-discount.php?status=invalid_edit");
            exit();
        }
    }
}

