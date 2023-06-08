<?php

if (isset($_POST['username']) && isset($_POST['email'])) {
    include('../database/db.php');
    $email = $_POST['email'];
    $username = $_POST['username'];

    // Check if email exists
    $email_sql = "SELECT * FROM customer_user WHERE EMAIL = '$email'";
    $email_result = $conn->query($email_sql);
    if ($email_result->num_rows > 0) {
        echo '1';
    } else {
        // Check if username exists
        $username_sql = "SELECT * FROM customer_user WHERE USERNAME = '$username'";
        $username_result = $conn->query($username_sql);
        if ($username_result->num_rows > 0) {
            echo '2';
        } else {
            echo '0';
        }
    }
} else {
    header('Location: ../index.php');
}
