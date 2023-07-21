<?php
include('../database/db.php');
if (isset($_POST['uid']) && isset($_POST['acc_type']) && isset($_POST['password']) && isset($_POST['data'])) {
    if ($_POST['data'] === 'asdjagsdhashdgahsgdajgdsjghasydtqtwye') {
        $id = $_POST['uid'];
        $acc_type = $_POST['acc_type'];
        $password = $_POST['password'];
        $hash_pw = password_hash($password, PASSWORD_DEFAULT);

        if ($acc_type === 'emp') {
            $cp_sql = "UPDATE `employee` SET `PASSWORD`='$hash_pw' WHERE EMP_ID ='$id'";
        } else {
            $cp_sql = "UPDATE `customer_user` SET `PASSWORD` = '$hash_pw' WHERE CUST_ID = '$id'";
        }

        if ($conn->query($cp_sql) === TRUE) {
            echo 'Password Changed';
        } else {
            echo 'Not';
        }
    }
}
