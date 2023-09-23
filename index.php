<?php
error_reporting(0);
$invalid = false;

if (isset($_POST['login'])) {
    include('database/db.php');

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $usernameCheck = "SELECT * FROM employee WHERE BINARY USERNAME = '$username' OR BINARY EMAIL = '$username'";
    $usernameResult = $conn->query($usernameCheck);

    if ($usernameResult->num_rows > 0) {
        $employee = $usernameResult->fetch_assoc();
        if (password_verify($password, $employee['PASSWORD'])) {
            if ($employee['EMP_STATUS'] === 'active') {
                include('time-date.php');

                $emp_id = $employee["EMP_ID"];
                session_start();
                $_SESSION["id"] = $emp_id;

                $log_sql = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                    VALUES ('$emp_id','Log In','$currentDate','$currentTime')";
                $conn->query($log_sql);

                if ($employee['EMP_TYPE'] === 'Admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($employee['EMP_TYPE'] === 'PA' || $employee['EMP_TYPE'] === 'Pharmacists') {
                    header("Location: sales/pos.php");
                } elseif ($employee['EMP_STATUS'] === 'Rider') {
                    header("Location: rider.php");
                }
                exit;
            }
        } else {
            $invalid = true;
        }
    } else {
        $usernameCheck = "SELECT * FROM `customer_user` WHERE `USERNAME` = '$username' OR `EMAIL` = '$username'";
        $usernameResult = $conn->query($usernameCheck);

        if ($usernameResult->num_rows > 0) {
            $customer = $usernameResult->fetch_assoc();
            if (password_verify($password, $customer['PASSWORD'])) {
                if ($customer['STATUS'] === 'Active') {
                    header("Location: download-app.html");
                } else {
                    $invalid = true;
                }
            } else {
                $invalid = true;
            }
        } else {
            $invalid = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOrder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login-form.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
</head>

<body class="">
    <form class="main-container container" method="post" action="">
        <div class="inputs-container container">
            <img src="img/ggd-logo.png">

            <hr class="hr-logo">
            <?php if ($invalid == true) : ?>
                <div class="wrong-email-pass">
                    <em>Wrong email or password!</em>
                </div>
            <?php endif; ?>

            <div class="input-field">
                <input name="username" type="text" class="input" id="username" required autocomplete="off" placeholder="Username">
                <label for="username">Username</label>
            </div>
            <div class="input-field">
                <input name="password" type="password" class="input" id="password" required placeholder="Password">
                <button type="button" id="viewPassword"><i class="fa-regular fa-eye"></i></button>
                <label for="password">Password</label>
            </div>
            <div class="input-field">
                <input type="submit" class="submit btn btn-primary" value="Log in" name="login">
            </div>

            <a href="forgot-password.php" class="forgot-pass">Forgot password?</a>

            <hr class="hr-forgot-pass">

            <a href="signup.php" class="create-new-account btn">Create new account</a>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
    <script src="js/login.js"></script>
</body>

</html>