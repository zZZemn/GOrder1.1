<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['txt_add_region'])) {
        $new_region = filter_var($_POST['txt_add_region'], FILTER_SANITIZE_STRING);

        $check_region_exist = "SELECT * FROM region WHERE REGION = '$new_region'";
        $check_region_exist_result = $conn->query($check_region_exist);
        if ($check_region_exist_result->num_rows > 0) {
            echo "not inserted";
        } else {
            $new_region_id = 'RGN_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $check_new_region_id = "SELECT * FROM region WHERE REGION_ID = '$new_region_id'";
            $check_new_region_id_result = $conn->query($check_new_region_id);
            while ($check_new_region_id_result->num_rows > 0) {
                $new_region_id = 'RGN_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $check_new_region_id = "SELECT * FROM region WHERE REGION_ID = '$new_region_id'";
                $check_new_region_id_result = $conn->query($check_new_region_id);
            }

            $insert_new_region = "INSERT INTO `region`(`REGION_ID`, `REGION`, `REGION_STATUS`) 
                                                VALUES ('$new_region_id','$new_region','active')";

            $addDate = $currentDate;
            $addTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

            $add_region_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                                ('$emp_id','Add $new_region in address(region).','$addDate','$addTime')";

            if ($conn->query($insert_new_region) === TRUE && $conn->query($add_region_log) === TRUE) {
                echo "inserted";
            }else {
                echo "not inserted";
            }
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
