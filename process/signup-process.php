<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../PHPMailer/src/PHPMailer.php');
require('../PHPMailer/src/SMTP.php');
require('../PHPMailer/src/Exception.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['getVerificationCode'])) {
    include('../database/db.php');

    $sec = $_POST['getVerificationCode'];
    if ($sec == 1265376512) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $verification_code = rand(111111, 999999);

        // send email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ugabane0516@gmail.com';
        $mail->Password = 'owwj dmzb hypq lsfu';
        $mail->Port = 465; // or the appropriate SMTP port provided by Hostinger
        $mail->SMTPSecure = 'ssl'; // or 'ssl' if applicable

        $mail->setFrom('ugabane0516@gmail.com', 'GOrder');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
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

        if ($mail->send()) {
            $_SESSION['email_sent'] = true;
            $response = [$verification_code, $email];
            echo json_encode($response);
        } else {
            header('Location: ../signup.php');
            exit;
        }
    } else {
        echo '400';
    }
} else {
    header('Location: ../index.php');
    exit;
}
