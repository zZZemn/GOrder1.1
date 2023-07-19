<?php

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['new_cat'])) {
        $new_cat = filter_var($_POST['new_cat'], FILTER_SANITIZE_STRING);

        $new_cat_id = rand(1000, 9999);
        $cat_id_sql = "SELECT * FROM category WHERE CAT_ID = $new_cat_id";
        $cat_id_result = $conn->query($cat_id_sql);
        while ($cat_id_result->num_rows > 0) {
            $new_cat_id = rand(1000, 9999);
            $cat_id_sql = "SELECT * FROM category WHERE CAT_ID = $new_cat_id";
            $cat_id_result = $conn->query($cat_id_sql);
        }

        $insert_new_cat = "INSERT INTO `category`(`CAT_ID`, `CAT_NAME`) 
                                        VALUES ('$new_cat_id','$new_cat')";

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

        $add_cat_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                ('$emp_id','Add $new_cat in  category list.','$addDate','$addTime')";

        if ($conn->query($insert_new_cat) === TRUE && $conn->query($add_cat_log) === TRUE) {
            echo 'added';
        } else {
            echo 'invalid';
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}
