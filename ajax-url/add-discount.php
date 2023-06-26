<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['discountName']) && isset($_POST['discountPercentage'])) {
        $discount_name = $_POST['discountName'];
        $discount_percentage = $_POST['discountPercentage'];

        $discount_id = random_int(100, 999);
        $discount_sql = "SELECT * FROM discount WHERE DISCOUNT_ID = '$discount_id'";
        $discount_result = $conn->query($discount_sql);
        while ($discount_result->num_rows > 0) {
            $discount_id = random_int(100, 999);
            $discount_sql = "SELECT * FROM discount WHERE DISCOUNT_ID = '$discount_id'";
            $discount_result = $conn->query($discount_sql);
        }

        $insert_discount_sql = "INSERT INTO `discount`(`DISCOUNT_ID`, `DISCOUNT_NAME`, `DISCOUNT_PERCENTAGE`, `DISCOUNT_STATUS`) 
                                                    VALUES ('$discount_id','$discount_name','$discount_percentage', 'active')";

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

        $add_discount_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                        ('$emp_id','Add $discount_name in discount.','$addDate','$addTime')";

        if ($conn->query($insert_discount_sql) === TRUE && $conn->query($add_discount_log) === TRUE) {
            echo "inserted";
        } else {
            echo "invalid";
        }
    } else {
        echo "invalid";
    }
} else {
    header("Location: ../index.php");
    exit;
}
