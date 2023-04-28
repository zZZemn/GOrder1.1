<?php
session_start();
$_SESSION['authorized'] = false;

include('database/db.php');

if(isset($_POST['submit']))
{
    $_SESSION['authorized'] = true;

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];
    $bday = $_POST['bday'];
    $sex = $_POST['sex'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $unit = $_POST['unit'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cust_type = $_POST['cust_type'];
    $status = $_POST['status'];
    $cust_id = $_POST['cust_id'];
}

if(isset($_POST['agree']))
{
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];
    $bday = $_POST['bday'];
    $sex = $_POST['sex'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $unit = $_POST['unit'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cust_type = $_POST['cust_type'];
    $status = $_POST['status'];
    $cust_id = $_POST['cust_id'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO `customer_user`(`CUST_ID`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `EMAIL`, `USERNAME`, `PASSWORD`, `CONTACT_NO`, `UNIT_STREET`, `BARANGAY`, `MUNICIPALITY`, `PROVINCE`, `REGION`, `BIRTHDAY`, `CUSTOMER_TYPE`, `STATUS`) 
                                VALUES ('$cust_id','$fname','$lname','$mi','$suffix','$sex','$email','$username','$hashed_password','$contact_no','$unit','$barangay','$municipality','$province','$region','$bday','$cust_type','$status')";

    $result = $conn->query($sql);
    if($result)
    {
        header("Location: download-app.html");
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/access-denied.css">
    <link rel="stylesheet" href="css/terms-condition.css">
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
            crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <title>Terms and Condition</title>
</head>
<body>
    <?php if ($_SESSION['authorized']):?>
    <div class="terms-condition">
        <h1><em>GOrder</em> Terms and Conditions</h1>
        <p>• You retain ownership of any content that you submit, post, or display on
            our platform, but you grant us a non-exclusive, transferable, sub-licensable,
            royalty-free, worldwide license to use, copy, modify, create derivative works
            based on, distribute, publicly display, publicly perform, and otherwise exploit
            in any manner such content in all formats and distribution channels now known or
            hereafter devised.</p>
        <p>• You represent and warrant that you have all necessary rights to grant the
            license described in Section 2.1 and that your content does not infringe or
            violate the rights of any third party.</p>
        <p>• You are solely responsible for any content that you submit, post, or
            display on our platform, and for any consequences thereof.</p>
        <p>• We reserve the right to remove any content that violates these Terms and
            Conditions, or that we otherwise deem objectionable in our sole discretion.</p>
        <form method="post">
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
            <input type="submit" name="agree" class="btn btn-primary" value="Agree">
        </form>
    </div>
    <?php else: ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>
</html>