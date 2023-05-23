<?php

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['submit_new_cat'])) {
        $cat_id = $_POST['cat_id'];
        $new_sub_cat = filter_var($_POST['add_sub_cat'], FILTER_SANITIZE_STRING);

        $new_sub_cat_id = rand(10000, 99999);
        $sub_cat_id_sql = "SELECT * FROM sub_category WHERE SUB_CAT_ID = $new_sub_cat_id";
        $sub_cat_id_result = $conn->query($sub_cat_id_sql);
        while ($sub_cat_id_result->num_rows > 0) {
            $new_sub_cat_id = rand(10000, 99999);
            $sub_cat_id_sql = "SELECT * FROM sub_category WHERE SUB_CAT_ID = $new_sub_cat_id";
            $sub_cat_id_result = $conn->query($sub_cat_id_sql);
        }

        $insert_new_subcat = "INSERT INTO `sub_category`(`SUB_CAT_ID`, `CAT_ID`, `SUB_CAT_NAME`) 
                                                VALUES ('$new_sub_cat_id','$cat_id','$new_sub_cat')";

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

        $add_subcat_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                ('$emp_id','Add $new_sub_cat in sub category list.','$addDate','$addTime')";

        if ($conn->query($insert_new_subcat) === TRUE && $conn->query($add_subcat_log) === TRUE) {
            header("Location: ../admin/maintenance-category.php?status=success");
            exit();
        } else {
            header("Location: ../admin/maintenance-category.php?status=invalid_add");
            exit();
        }
    }
}else{
    header("Location: ../index.php");
    exit();
}

