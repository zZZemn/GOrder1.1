<?php
session_start();

// ob_start();
// include('../signup.php');
// ob_end_clean();

require('../PHPMailer/src/PHPMailer.php');
require('../PHPMailer/src/SMTP.php');


if(isset($_SESSION['authorized']))
{
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    $mi = $_SESSION['mi'];
    $suffix = $_SESSION['suffix'];

    $bday = $_SESSION['bday'];
    $sex = $_SESSION['sex'];

    $contact_no = $_SESSION['contact_no'];
    $email = $_SESSION['email'];

    $unit = $_SESSION['unit'];
    $region = $_SESSION['region'];
    $province = $_SESSION['province'];
    $municipality = $_SESSION['municipality'];
    $barangay = $_SESSION['barangay'];

    $username = $_SESSION['username']; 
    $password = $_SESSION['password'];


    include('../database/db.php');

    $cust_type = "Regular";
    $status = "Active";

    $cust_id = rand(100000, 999999);
    $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");

    while($cust_id_result->num_rows > 0)
    {
        $cust_id = rand(100000, 999999);
        $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");
    }

    $verification_code = rand(100000, 999999);

    //Email 
    $mailTo = $email;
    $body = "Hello $fname "."$lname your verification code is <h1>$verification_code</h1>";

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP();

    $mail->Host = "mail.smtp2go.com";

    $mail->SMTPAuth = true;

    $mail->Username = "GOrder";
    $mail->Password = "NIneNDqZShCavtWC";

    $mail->SMTPSecure = "tls";

    $mail->Port = "2525";

    $mail->From = "ugabanemmanuel@outlook.com";

    $mail->FromName = "GOrder";

    $mail->addAddress($mailTo, $fname);

    $mail->isHTML(true);

    $mail->Subject = "GOrder Verification Code";
    $mail->Body = $body;
    $mail->AltBody = $body;

    if(!$mail->send())
    {
        echo "Mailer Error: ".$mail->ErrorInfo;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/signup-process.css">
        <link rel="stylesheet" href="../css/access-denied.css">
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
            crossorigin="anonymous">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
        </style>
        <title>Verification Code</title>
    </head>
    <body>

    <?php if ($_SESSION['authorized']):?>
        <form
            action="../terms-and-condition.php"
            method="post"
            class="verification-container">
            <input type="hidden" name="fname" value="<?php echo $fname ?>">
            <input type="hidden" name="lname" value="<?php echo $lname ?>">
            <input type="hidden" name="mi" value="<?php echo $mi ?>">
            <input type="hidden" name="suffix" value="<?php echo $suffix ?>">
            <input type="hidden" name="bday" value="<?php echo $bday ?>">
            <input type="hidden" name="sex" value="<?php echo $sex ?>">
            <input type="hidden" name="contact_no" value="<?php echo $contact_no ?>">
            <input type="hidden" name="email" value="<?php echo $email ?>">
            <input type="hidden" name="unit" value="<?php echo $unit ?>">
            <input type="hidden" name="region" value="<?php echo $region ?>">
            <input type="hidden" name="province" value="<?php echo $province ?>">
            <input type="hidden" name="municipality" value="<?php echo $municipality ?>">
            <input type="hidden" name="barangay" value="<?php echo $barangay ?>">
            <input type="hidden" name="username" value="<?php echo $username ?>">
            <input type="hidden" name="password" value="<?php echo $password ?>">
            <input type="hidden" name="cust_type" value="<?php echo $cust_type ?>">
            <input type="hidden" name="status" value="<?php echo $status ?>">
            <input type="hidden" name="cust_id" value="<?php echo $cust_id ?>">

            <input
                type="hidden"
                id="random-verification-code"
                value="<?php echo $verification_code ?>">
            <div id="message">
                Enter Verification Code
            </div>
            <input
                type="text"
                name="verification_code"
                id="verification_code"
                maxlength="6"
                oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                required="required">
            <input
                type="submit"
                name="submit"
                id="submit-verification"
                disabled="disabled"
                class="btn btn-primary">
        </form>
    <?php else: ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#verification_code').on('input', function () {
                    var input_code = $('#verification_code').val();
                    var actual_code = $('#random-verification-code').val();
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
            window.onbeforeunload = function () {
                return false;
            };
        </script>
    </body>
</html>