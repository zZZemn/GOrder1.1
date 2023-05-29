<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp['EMP_TYPE'] === 'Admin' && isset($_GET['del_id'])) {
        $delID = $_GET['del_id'];
        if (is_numeric($delID)) {
            include('../time-date.php');
            $editDate = $currentDate;
            $editTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

            $deleteDelivery = "UPDATE `delivery` SET `DELIVERY_STATUS`='deleted' WHERE DELIVERY_ID = $delID";

            $del_del_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Delete Delivery  $delID','$editDate','$editTime')";

            if ($conn->query($deleteDelivery) === TRUE && $conn->query($del_del_log) === TRUE) {
                header("Location: ../admin/products-deliver.php?status=deletion_success");
                exit;
            } else {
                header("Location: ../admin/products-deliver.php?status=deletion_failed");
                exit;
            }
        } else {
            header("Location: ../admin/products-deliver.php?status=deletion_failed");
            exit;
        }
    } else {
        header("Location: ../admin/products-deliver.php?status=deletion_failed");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
