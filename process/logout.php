<?php

session_start();
if(isset($_SESSION['id']))
{
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_id = $emp['EMP_ID'];

    $log_sql = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
    VALUES ('$emp_id','Log Out',' $currentDate','$currentTime')";
    $conn->query($log_sql);
}

session_destroy();

header("Location: ../index.php");

exit;