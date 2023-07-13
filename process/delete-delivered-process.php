<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp['EMP_TYPE'] === 'Admin' && isset($_GET['inv_id'])) {
        $inv_id = $_GET['inv_id'];
        if (is_numeric($inv_id)) {
            include('../time-date.php');
            $editDate = $currentDate;
            $editTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

            $deleteInventory= "DELETE FROM `inventory` WHERE INV_ID = '$inv_id'";

            $del_del_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Delete INV-$inv_id in inventory','$editDate','$editTime')";

            if ($conn->query($deleteInventory) === TRUE && $conn->query($del_del_log) === TRUE) {
                echo 'ok';
            } else {
                echo 'not';
            }
        } else {
            echo 'not';
        }
    } else {
        echo 'not';
    }
} else {
    header("Location: ../index.php");
    exit;
}
