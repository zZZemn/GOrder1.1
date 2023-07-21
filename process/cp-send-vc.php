<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../PHPMailer/src/PHPMailer.php');
require('../PHPMailer/src/SMTP.php');
require('../PHPMailer/src/Exception.php');
include('../database/db.php');
if (isset($_POST['id']) && isset($_POST['user_id']) && isset($_POST['email']) && isset($_POST['acc_type'])) {
    $random_id = $_POST['id'];
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $acc_type = $_POST['acc_type'];
    if ($random_id === 'ajskhdjkashznbxcnzbxchasd') {
        $verification_code = rand(111111, 999999);

        if ($acc_type === 'emp') {
            $user_sql = "SELECT FIRST_NAME, LAST_NAME FROM employee WHERE EMP_ID = '$user_id'";
        } else {
            $user_sql = "SELECT FIRST_NAME, LAST_NAME FROM customer_user WHERE CUST_ID = '$user_id'";
        }
        $user_result = $conn->query($user_sql);
        if ($user_result !== false && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $first_name = $user['FIRST_NAME'];
            $last_name = $user['LAST_NAME'];

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'goldengate.gorder@gmail.com';
            $mail->Password = 'igwuzryiyygirllf';
            $mail->SMTPSecure = 'ssl'; // or 'ssl' if applicable
            $mail->Port = 465; // or the appropriate SMTP port provided by Hostinger

            $mail->setFrom('goldengate.gorder@gmail.com', 'GOrder');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Change Password';
            $mail->Body = '
                            <html>
                            <head>
                                <style>
                                    body {
                                        font-family: Arial, sans-serif;
                                        background-color: #f1f1f1;
                                        padding: 20px;
                                    }
                                    .container {
                                        background-color: #fff;
                                        border-radius: 5px;
                                        padding: 20px;
                                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                    }
                                    h1 {
                                        color: #333;
                                    }
                                    p {
                                        color: #777;
                                        margin-bottom: 10px;
                                    }
                                    .verification-code {
                                        font-size: 24px;
                                        font-weight: bold;
                                        color: #007bff;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="container">
                                    <h1>Verification Code</h1>
                                    <p>Hi ' . $first_name . ' ' . $last_name . ',</p>
                                    <p>Your verification code is:</p>
                                    <p class="verification-code">' . $verification_code . '</p>
                                </div>
                            </body>
                            </html>';
            if ($mail->send() && !isset($_SESSION['email_sent'])) {
                $_SESSION['email_sent'] = true;
                $response = [$verification_code, $user_id, $email, $acc_type, $first_name, $last_name];
                echo json_encode($response);
            } else {
                echo $mail->isError();
            }
        } else {
            echo 'not found';
        }
    } else {
        echo 'random-id';
    }
} else {
    echo 'acc-type';
}
