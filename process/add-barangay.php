<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
    if (isset($emp) && $emp['EMP_STATUS'] == "active") {
        if(isset($_POST['bgy'])){
            $bgyid = $_POST['bgy'];
            $check_bgy_sql = "SELECT BARANGAY FROM barangay WHERE BARANGAY_ID = '$bgyid'";
            $check_bgy_result = $conn->query($check_bgy_sql);
            if($check_bgy_result->num_rows > 0){
                $update_bgy_sql = "UPDATE `barangay` SET `BARANGAY_STATUS`='active' WHERE BARANGAY_ID = '$bgyid'";
                if($conn->query($update_bgy_sql) === TRUE){
                    echo 'updated';
                } else {
                    echo 'not updated';
                }
            } else {
                echo 'Barangay ID Not Valid';
            }
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
