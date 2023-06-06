<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../PHPMailer/src/PHPMailer.php');
require('../PHPMailer/src/SMTP.php');
require('../PHPMailer/src/Exception.php');

if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['first_name']) && isset($_POST['barangay'])) {
    include('../database/db.php');

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];

    $birthday = $_POST['birthday'];
    $sex = $_POST['sex'];
    $contact = $_POST['contact'];
    $unit = $_POST['unit'];
    $barangay = $_POST['barangay'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];


    $verification_code = rand(111111, 999999);

    $cust_id = rand(11111111, 99999999);
    $cust_id_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
    $cust_id_result = $conn->query($cust_id_sql);
    while ($cust_id_result->num_rows > 0) {
        $cust_id = rand(11111111, 99999999);
        $cust_id_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
        $cust_id_result = $conn->query($cust_id_sql);
    }

    $cart_id = rand(1111111, 9999999);
    $cart_id_sql = "SELECT * FROM cart WHERE CART_ID = '$cart_id'";
    $cart_id_result = $conn->query($cart_id_sql);
    while ($cart_id_result->num_rows > 0) {
        $cart_id = rand(1111111, 9999999);
        $cart_id_sql = "SELECT * FROM cart WHERE CART_ID = '$cart_id'";
        $cart_id_result = $conn->query($cart_id_sql);
    }

    // send email
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
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../css/signup-process.css">
            <link rel="stylesheet" href="../css/access-denied.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
            </style>
            <title>Verification Code</title>
        </head>

        <body>
            <form action="../terms-and-condition.php" method="post" class="verification-container">
                <input type="hidden" name="fname" value="<?php echo $first_name ?>">
                <input type="hidden" name="lname" value="<?php echo $last_name ?>">
                <input type="hidden" name="mi" value="<?php echo $mi ?>">
                <input type="hidden" name="suffix" value="<?php echo $suffix ?>">
                <input type="hidden" name="bday" value="<?php echo $birthday ?>">
                <input type="hidden" name="sex" value="<?php echo $sex ?>">
                <input type="hidden" name="contact_no" value="<?php echo $contact ?>">
                <input type="hidden" name="email" value="<?php echo $email ?>">
                <input type="hidden" name="unit" value="<?php echo $unit ?>">
                <input type="hidden" name="barangay" value="<?php echo $barangay ?>">
                <input type="hidden" name="username" value="<?php echo $username ?>">
                <input type="hidden" name="password" value="<?php echo $password ?>">
                <input type="hidden" name="cust_id" value="<?php echo $cust_id ?>">
                <input type="hidden" name="cart_id" value="<?php echo $cart_id ?>">

                <h1><?php echo $verification_code ?></h1>
                <div id="message">
                    Enter Verification Code
                </div>
                <input type="text" name="verification_code" id="verification_code" maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required="required">
                <input type="submit" name="submit" id="submit-verification" disabled="disabled" class="btn btn-primary">
            </form>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#verification_code').on('input', function() {
                        var input_code = $('#verification_code').val();
                        var actual_code = <?php echo $verification_code ?>;
                        if (input_code == actual_code) {
                            $('#message').css('color', 'green');
                            $('#message').css('font-family', 'Verdana, sans-serif');
                            $('#message').text('Verification code matched');
                            $('#submit-verification').prop('disabled', false);
                        } else {
                            $('#message').css('color', 'red');
                            $('#message').css('font-family', 'Helvetica, sans-serif');
                            $('#message').text('Verification code does not match');
                            $('#submit-verification').prop('disabled', true);
                        }
                    });
                });
            </script>
            <script>
                window.onbeforeunload = function() {
                    return false;
                };
            </script>
        </body>

        </html>
<?php
    } else {
        echo 'Error sending email: ' . $mail->ErrorInfo;
    }
} else {
    header('Location: ../index.php');
    exit;
}
