<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['oldPw'], $_POST['newPw'])) {
        $oldPw = $_POST['oldPw'];
        $newPw = $_POST['newPw'];
        $pw = password_hash($newPw, PASSWORD_DEFAULT);

        if (password_verify($oldPw, $emp['PASSWORD'])) {
            $update_sql = "UPDATE `employee` SET `PASSWORD`='$pw' WHERE `EMP_ID` = '{$_SESSION['id']}'";
            if ($conn->query($update_sql)) {
                echo '200';
            } else {
                echo '400';
            }
        } else {
            echo '405';
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
    exit;
}
