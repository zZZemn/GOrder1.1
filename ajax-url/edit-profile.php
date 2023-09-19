<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['fname'])) {
        $first_name = $_POST["fname"];
        $last_name = $_POST["lname"];
        $middle_initial = $_POST["mi"];
        $suffix = $_POST["suffix"];
        $sex = $_POST["sex"];
        $birthday = $_POST["birthday"];
        $username = $_POST["username"];
        $contact_no = $_POST["contact"];
        $email = $_POST["email"];
        $address = $_POST["address"];

        $update = "UPDATE `employee` SET `FIRST_NAME`='$first_name',`LAST_NAME`='$last_name',`MIDDLE_INITIAL`='$middle_initial',`SUFFIX`='$suffix',`SEX`='$sex',`EMAIL`='$email',`USERNAME`='$username',`CONTACT_NO`='$contact_no',`ADDRESS`='$address',`BIRTHDAY`='$birthday' WHERE `EMP_ID` = {$_SESSION['id']}";
        if ($conn->query($update)) {
            echo '200';
        } else {
            echo '400';
        }
    } else {
        echo '405';
    }
} else {
    header("Location: ../index.php");
    exit;
}
