<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['txt_add_municipality']) && isset($_POST['province_id'])) {
        $province_id = filter_var($_POST['province_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_municipality = filter_var($_POST['txt_add_municipality'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $check_municipality_exist = "SELECT * FROM municipality WHERE MUNICIPALITY = '$new_municipality'";
        $check_municipality_exist_result = $conn->query($check_municipality_exist);
        if ($check_municipality_exist_result->num_rows > 0) {
            echo "not inserted";
        } else {
            $new_municipality_id = 'MUNI_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $check_new_municipality_id = "SELECT * FROM municipality WHERE MUNICIPALITY_ID = '$new_municipality_id'";
            $check_new_municipality_id_result = $conn->query($check_new_municipality_id);
            while ($check_new_municipality_id_result->num_rows > 0) {
                $new_municipality_id = 'MUNI_' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $check_new_municipality_id = "SELECT * FROM municipality WHERE MUNICIPALITY_ID = '$new_municipality_id'";
                $check_new_municipality_id_result = $conn->query($check_new_municipality_id);
            }

            $insert_new_municipality = "INSERT INTO `municipality`(`PROVINCE_ID`, `MUNICIPALITY_ID`, `MUNICIPALITY`, `MUNICIPALITY_STATUS`) 
                                                            VALUES ('$province_id','$new_municipality_id','$new_municipality','active')";

            $addDate = $currentDate;
            $addTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

            $add_municipality_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                                ('$emp_id','Add $new_municipality in address(municipality).','$addDate','$addTime')";

            if ($conn->query($insert_new_municipality) === TRUE && $conn->query($add_municipality_log) === TRUE) {
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
