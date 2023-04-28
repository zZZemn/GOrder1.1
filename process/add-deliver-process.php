<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if (isset($_POST['add_deliver'])) {
    include('../time-date.php');

    $del_id = mt_rand(100000, 999999);

    $check_del_id = "SELECT * FROM delivery WHERE DELIVERY_ID = $del_id";
    $check_del_id_result = $conn->query($check_del_id);
    while ($check_del_id_result->num_rows > 0) {
        $del_id = mt_rand(1000, 9999);
        $check_del_id = "SELECT * FROM delivery WHERE DELIVERY_ID = $del_id";
        $check_del_id_result = $conn->query($check_del_id);
    }

    $supp_id = filter_var($_POST['supplier_id'], FILTER_SANITIZE_NUMBER_INT);
    $del_date = filter_var($_POST['delivery_date'], FILTER_SANITIZE_STRING);

    $add_del_sql = "INSERT INTO `delivery`(`DELIVERY_ID`, `SUPPLIER_ID`, `DELIVERY_DATE`, `DELIVERY_STATUS`) 
                                    VALUES ('$del_id','$supp_id','$del_date','active')";

    $addDate = $currentDate;
    $addTime = $currentTime;
    $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

    $add_sup_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Add New Deliver Products.','$addDate','$addTime')";

    if ($conn->query($add_del_sql) === TRUE && $conn->query($add_sup_log) === TRUE) {
        header("Location: ../admin/products-deliver.php?status=success");
        exit();
    } else {
        header("Location: ../admin/products-deliver.php?status=invalid_add");
        exit();
    }
}
