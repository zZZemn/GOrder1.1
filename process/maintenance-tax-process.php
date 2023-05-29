<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['save_tax_rate'])) {
        $emp_id = $emp['EMP_ID'];
        $new_tax_rate = $_POST['tax_rate'];
        include('../time-date.php');
        $addDate = $currentDate;
        $addTime = $currentTime;

        $update_tax_rate_sql = "UPDATE `tax` SET `TAX_PERCENTAGE`='$new_tax_rate' WHERE 1";

        $edit_tax_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Update TAX rate to $new_tax_rate.','$addDate','$addTime')";

        if ($conn->query($update_tax_rate_sql) === TRUE && $conn->query($edit_tax_log) === TRUE) {
            header("Location: ../admin/maintenance-tax.php?status=edited");
            exit();
        } else {
            header("Location: ../admin/maintenance-tax.php?status=invalid_edit");
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
