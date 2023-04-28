<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if (isset($_POST['edit_deliver'])) {
    include('../time-date.php');

    $deliveryID = $_POST['deliver_id'];

    $supp_id = filter_var($_POST['supplier_id'], FILTER_SANITIZE_NUMBER_INT);
    $del_date = filter_var($_POST['delivery_date'], FILTER_SANITIZE_STRING);

    $edit_del_sql = "UPDATE `delivery` SET `SUPPLIER_ID`='$supp_id',`DELIVERY_DATE`='$del_date' WHERE DELIVERY_ID = $deliveryID";

    $editDate = $currentDate;
    $editTime = $currentTime;
    $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

    $edit_sup_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Edit Delivery Details Of Delivery ID $deliveryID.','$editDate','$editTime')";

    if ($conn->query($edit_del_sql) === TRUE && $conn->query($edit_sup_log) === TRUE) {
        header("Location: ../admin/products-deliver.php?status=success");
        exit();
    } else {
        header("Location: ../admin/products-deliver.php?status=invalid_edit");
        exit();
    }
}
