<?php

if (isset($_POST['username']) && isset($_POST['email'])) {
    include('../database/db.php');
    $email = $_POST['email'];
    $username = $_POST['username'];

    $user_sql = "SELECT * FROM customer_user WHERE USERNAME = 'username' OR EMAIL = '$email'";
    $user_result = $conn->query($user_sql);
    if($user_result->num_rows > 0){
        echo 'exists';
    } else {
        echo 'ok';
    }
} else {
    header('Location: ../index.php');
}
