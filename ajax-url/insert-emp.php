<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['f_name']) && isset($_POST['l_name']) && isset($_POST['mi']) && isset($_POST['suffix']) && isset($_POST['sex']) && isset($_POST['birthday']) && isset($_POST['emp_type']) && isset($_POST['email']) && isset($_POST['contact_no']) && isset($_POST['address']) && isset($_POST['username']) && isset($_POST['password'])) {
        $f_name = $_POST['f_name'];
        $l_name = $_POST['l_name'];
        $mi = $_POST['mi'];
        $suffix = $_POST['suffix'];
        $sex = $_POST['sex'];
        $birthday = $_POST['birthday'];
        $emp_type = $_POST['emp_type'];
        $email = $_POST['email'];
        $contact_no = $_POST['contact_no'];
        $address = $_POST['address'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $first_letter = ucfirst(substr($f_name, 0, 1));
        $picture = $first_letter . '.png';

        $new_emp_id = mt_rand(10000, 99999);
        $check_id_sql = "SELECT * FROM employee WHERE EMP_ID = '$new_emp_id'";
        $check_id_result = $conn->query($check_id_sql);
        while ($check_id_result->num_rows > 0) {
            $new_emp_id = mt_rand(10000, 99999);
            $check_id_sql = "SELECT * FROM employee WHERE EMP_ID = '$new_emp_id'";
            $check_id_result = $conn->query($check_id_sql);
        }

        $insert_new_emp = "INSERT INTO `employee`(`EMP_ID`, `EMP_TYPE`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `EMAIL`, `USERNAME`, `PASSWORD`, `CONTACT_NO`, `ADDRESS`, `PICTURE`, `BIRTHDAY`, `EMP_STATUS`) 
                                        VALUES ('$new_emp_id','$emp_type','$f_name','$l_name','$mi','$suffix','$sex','$email','$username','$hashedPassword','$contact_no','$address','$picture','$birthday','active')";

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

        $add_emp_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                                ('$emp_id','Created $f_name $l_name Account(Employee).','$addDate','$addTime')";

        if ($conn->query($insert_new_emp) === TRUE && $conn->query($add_emp_log) === TRUE) {
            echo "inserted";
        } else {
            echo $conn->error;
        }
    } else {
        echo "not inserted";
    }
} else {
    header("Location: ../index.php");
    exit;
}
