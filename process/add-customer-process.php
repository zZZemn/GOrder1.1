<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
    if (isset($emp) && $emp['EMP_STATUS'] == "active") {
        if (
            isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['mi']) &&
            isset($_POST['suffix']) &&
            isset($_POST['birthday']) &&
            isset($_POST['discount_type']) &&
            isset($_POST['contact_no']) &&
            isset($_POST['barangay']) &&
            isset($_POST['unit']) &&
            isset($_POST['sex'])
        ) {
            $fname = isset($_POST['fname']) ? $_POST['fname'] : '';
            $lname = isset($_POST['lname']) ? $_POST['lname'] : '';
            $mi = isset($_POST['mi']) ? $_POST['mi'] : '';
            $suffix = isset($_POST['suffix']) ? $_POST['suffix'] : '';
            $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
            $discount_type = isset($_POST['discount_type']) ? $_POST['discount_type'] : '';
            $contact_no = isset($_POST['contact_no']) ? $_POST['contact_no'] : '';
            $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : '';
            $unit = isset($_POST['unit']) ? $_POST['unit'] : '';
            $sex = isset($_POST['sex']) ? $_POST['sex'] : '';

            $cust_id = rand(11111111, 99999999);
            $cust_id_check = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'");
            while ($cust_id_check->num_rows > 0) {
                $cust_id = rand(11111111, 99999999);
                $cust_id_check = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'");
            }

            $firstLetter = substr($fname, 0, 1);
            $picture = $firstLetter . '.png';

            $cust_user_sql = "INSERT INTO `customer_user`(`CUST_ID`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `CONTACT_NO`, `UNIT_STREET`, `BARANGAY_ID`, `PICTURE`, `BIRTHDAY`, `DISCOUNT_TYPE`, `STATUS`) 
                                                VALUES ('$cust_id','$fname','$lname','$mi','$suffix','$sex','$contact_no','$unit','$barangay','$picture','$birthday','$discount_type','active')";

            if ($conn->query($cust_user_sql) === TRUE) {
                echo 'added';
            } else {
                echo 'not';
            }
        } else {
            echo "<title>Access Denied</title>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Sorry, you are not authorized to access this page.</h5>
                    </div>";
        }
    } else {
        echo "<title>Access Denied</title>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Sorry, you are not authorized to access this page.</h5>
                    </div>";
    }
} else {
    header("Location: ../index.php");
    exit;
}
