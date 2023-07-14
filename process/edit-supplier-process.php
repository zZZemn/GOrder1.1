<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['edit_product'])) {
        include('../time-date.php');

        $empID = $emp['EMP_ID'];
        $editTime = $currentTime;
        $editDate = $currentDate;

        $supp_id = filter_input(INPUT_POST, 'supp_id', FILTER_SANITIZE_NUMBER_INT);

        $supp_name = filter_input(INPUT_POST, 'supp_name', FILTER_SANITIZE_STRING);
        $supp_address = filter_input(INPUT_POST, 'supp_address', FILTER_SANITIZE_STRING);
        $contact_person = filter_input(INPUT_POST, 'contact_person', FILTER_SANITIZE_STRING);
        $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);

        $edit_supp_sql = "UPDATE `supplier` SET `NAME`='$supp_name',`ADDRESS`='$supp_address',`CONTACT_PERSON`='$contact_person',`CONTACT_NO`='$contact_number' WHERE `SUPPLIER_ID` = '$supp_id'";

        $edit_supp_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                    VALUES ('$empID','Edit Supplier $supp_id details','$editDate','$editTime')";

        if ($conn->query($edit_supp_sql) === TRUE && $conn->query($edit_supp_log) === TRUE) {
            echo 'ok';
        } else {
            echo 'not okay';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
