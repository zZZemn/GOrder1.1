<?php

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['cust_id']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['mi']) && isset($_POST['suffix']) && isset($_POST['sex']) && isset($_POST['birthday']) && isset($_POST['discountType']) && isset($_POST['barangay']) && isset($_POST['unit']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['contactNo'])) {
        $cust_id = $_POST['cust_id'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $mi = $_POST['mi'];
        $suffix = $_POST['suffix'];
        $sex = $_POST['sex'];
        $birthday = $_POST['birthday'];
        $discountType = $_POST['discountType'];
        $barangay = $_POST['barangay'];
        $unit = $_POST['unit'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $contactNo = $_POST['contactNo'];

        $cust_update_sql = "UPDATE `customer_user` SET `FIRST_NAME`='$fname',`LAST_NAME`='$lname',`MIDDLE_INITIAL`='$mi',`SUFFIX`='$suffix',`SEX`='$sex',`EMAIL`='$email',`USERNAME`='$username', `CONTACT_NO`='$contactNo',`UNIT_STREET`='$unit',`BARANGAY_ID`='$barangay', `BIRTHDAY`='$birthday',`DISCOUNT_TYPE`='$discountType' WHERE CUST_ID = '$cust_id'";
        if ($conn->query($cust_update_sql) === TRUE) {
            echo 'edited';
        } else {
            echo 'not-edited';
        }
    } else {
        echo 'not-set';
    }
} else {
    header("Location: ../index.php");
    exit;
}
