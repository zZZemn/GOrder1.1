<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['txt_add_province']) && isset($_POST['region_id'])) {
        $region_id = filter_var($_POST['region_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_province = filter_var($_POST['txt_add_province'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $check_province_exist = "SELECT * FROM province WHERE PROVINCE = '$new_province'";
        $check_province_exist_result = $conn->query($check_province_exist);
        if ($check_province_exist_result->num_rows > 0) {
            echo "not inserted";
        } else {
            $new_province_id = 'PRO_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $check_new_province_id = "SELECT * FROM province WHERE PROVINCE_ID = '$new_province_id'";
            $check_new_province_id_result = $conn->query($check_new_province_id);
            while ($check_new_province_id_result->num_rows > 0) {
                $new_province_id = 'PRO_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $check_new_province_id = "SELECT * FROM province WHERE PROVINCE_ID = '$new_province_id'";
            $check_new_province_id_result = $conn->query($check_new_province_id);
            }

            $insert_new_province = "INSERT INTO `province`(`REGION_ID`, `PROVINCE_ID`, `PROVINCE`, `PROVINCE_STATUS`) 
                                                    VALUES ('$region_id','$new_province_id','$new_province','active')";

            $addDate = $currentDate;
            $addTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

            $add_province_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                                ('$emp_id','Add $new_province in address(province).','$addDate','$addTime')";

            if ($conn->query($insert_new_province) === TRUE && $conn->query($add_province_log) === TRUE) {
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
