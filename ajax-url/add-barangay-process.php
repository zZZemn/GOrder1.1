<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['municipality_id']) && isset($_POST['barangay']) && isset($_POST['df'])) {
        $municipality_id = filter_var($_POST['municipality_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_barangay = filter_var($_POST['barangay'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $df = filter_var($_POST['df'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $check_barangay_exist = "SELECT * FROM barangay WHERE BARANGAY = '$new_barangay'";
        $check_barangay_exist_result = $conn->query($check_barangay_exist);
        if ($check_barangay_exist_result->num_rows > 0) {
            echo "not inserted";
        } else {
            $new_barangay_id = 'BGY_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $check_new_barangay_id = "SELECT * FROM barangay WHERE BARANGAY_ID = '$new_barangay_id'";
            $check_new_barangay_id_result = $conn->query($check_new_barangay_id);
            while ($check_new_barangay_id_result->num_rows > 0) {
                $new_barangay_id = 'BGY_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $check_new_barangay_id = "SELECT * FROM barangay WHERE BARANGAY_ID = '$new_barangay_id'";
                $check_new_barangay_id_result = $conn->query($check_new_barangay_id);
            }

            $insert_new_barangay = "INSERT INTO `barangay`(`MUNICIPALITY_ID`, `BARANGAY_ID`, `BARANGAY`, `DELIVERY_FEE`, `BARANGAY_STATUS`) 
                                                    VALUES ('$municipality_id','$new_barangay_id','$new_barangay','$df','active')";

            $addDate = $currentDate;
            $addTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

            $add_barangay_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                                ('$emp_id','Add $new_barangay in address(barangay).','$addDate','$addTime')";

            if ($conn->query($insert_new_barangay) === TRUE && $conn->query($add_barangay_log) === TRUE) {
                echo "inserted";
            }else {
                echo "not inserted";
            }
        }
    } else {
        echo "not inserted";
    }
} else {
    header("Location: ../index.php");
    exit;
}
