<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $check_result = $conn->query("SELECT * FROM employee WHERE `EMP_ID` = '$id'");
            if ($check_result->num_rows > 0) {
                $id = $_POST['id'];
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $mi = $_POST['mi'];
                $suffix = $_POST['suffix'];
                $sex = $_POST['sex'];
                $bday = $_POST['bday'];
                $emp_type = $_POST['emp_type'];
                $email = $_POST['email'];
                $contact_no = $_POST['contact_no'];
                $address = $_POST['address'];
                $username = $_POST['username'];

                $update_sql = "UPDATE `employee` SET 
                              `EMP_TYPE`='$emp_type',
                              `FIRST_NAME`='$fname',
                              `LAST_NAME`='$lname',
                              `MIDDLE_INITIAL`='$mi',
                              `SUFFIX`='$suffix',
                              `SEX`='$sex',
                              `EMAIL`='$email',
                              `USERNAME`='$username',
                              `CONTACT_NO`='$contact_no',
                              `ADDRESS`='$address',
                              `BIRTHDAY`='$bday'
                              WHERE `EMP_ID` = '$id'";
                if ($conn->query($update_sql)) {
                    echo 'success';
                } else {
                    echo 400;
                }
            } else {
                echo 505;
            }
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
