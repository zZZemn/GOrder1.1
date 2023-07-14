<?php

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['addSup'])) {
        include('../time-date.php');

        $sup_id = mt_rand(1000, 9999);
        $check_sup_id = "SELECT * FROM supplier WHERE SUPPLIER_ID = '$sup_id'";
        $check_sup_id_result = $conn->query($check_sup_id);
        while ($check_sup_id_result->num_rows > 0) {
            $sup_id = mt_rand(1000, 9999);
            $check_sup_id = "SELECT * FROM supplier WHERE SUPPLIER_ID = '$sup_id'";
            $check_sup_id_result = $conn->query($check_sup_id);
        }

        $name = filter_var($_POST['supp_name'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['supp_address'], FILTER_SANITIZE_STRING);
        $contact_person = filter_var($_POST['contact_person'], FILTER_SANITIZE_STRING);
        $contact_no = filter_var($_POST['contact_number'], FILTER_SANITIZE_NUMBER_INT);

        $insert_supplier = "INSERT INTO `supplier`(`SUPPLIER_ID`, `NAME`, `ADDRESS`, `CONTACT_PERSON`, `CONTACT_NO`, `SUPPLIER_STATUS`) 
                        VALUES ('$sup_id','$name','$address','$contact_person','$contact_no','active')";

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

        $add_sup_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Add $name in supplier list.','$addDate','$addTime')";

        if ($conn->query($insert_supplier) === TRUE && $conn->query($add_sup_log) === TRUE) {
            echo 'ok';
        } else {
            echo 'not ok';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
