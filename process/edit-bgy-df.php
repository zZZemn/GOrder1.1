<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
    if (isset($emp) && $emp['EMP_TYPE'] == "Admin" && $emp['EMP_STATUS'] == "active") {
        if (isset($_POST['new_df']) && isset($_POST['bgy_id'])) {
            $new_df = $_POST['new_df'];
            $bgy_id = $_POST['bgy_id'];

            $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
            $bgy_result = $conn->query($bgy_sql);
            if ($bgy_result->num_rows > 0) {
                $bgy_update = "UPDATE `barangay` SET `DELIVERY_FEE`='$new_df' WHERE BARANGAY_ID = '$bgy_id'";
                if ($conn->query($bgy_update) === TRUE) {
                    echo 'success';
                } else {
                    echo 'query failed';
                }
            } else {
                echo 'bgy_id not available';
            }
        } elseif (isset($_POST['bgy_id']) && isset($_POST['delete_this'])) {
            $bgy_id = $_POST['bgy_id'];
            $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
            $bgy_result = $conn->query($bgy_sql);
            if ($bgy_result->num_rows > 0) {
                $bgy_update = "UPDATE `barangay` SET `BARANGAY_STATUS`='deactivate' WHERE BARANGAY_ID = '$bgy_id'";
                if ($conn->query($bgy_update) === TRUE) {
                    echo 'success';
                } else {
                    echo 'query failed';
                }
            } else {
                echo 'bgy_id not available';
            }
        } else {
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
