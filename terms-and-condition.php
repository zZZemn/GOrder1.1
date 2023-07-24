<?php
error_reporting(0);
include('database/db.php');
include('time-date.php');

if (isset($_POST['agree'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];
    $bday = $_POST['bday'];
    $sex = $_POST['sex'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $unit = $_POST['unit'];
    $barangay = $_POST['barangay'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cust_id = $_POST['cust_id'];
    $cart_id = $_POST['cart_id'];

    $firstLetter = strtoupper(substr($fname, 0, 1));
    echo $firstLetter;
    $picture = $firstLetter . '.png';

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql_cart = "INSERT INTO `cart`(`CART_ID`) VALUES ('$cart_id')";
    if ($conn->query($sql_cart) === TRUE) {
        $sql = "INSERT INTO `customer_user`(`CUST_ID`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `EMAIL`, `USERNAME`, `PASSWORD`, `CONTACT_NO`, `UNIT_STREET`, `BARANGAY_ID`, `PICTURE`, `BIRTHDAY`,`CART_ID`, `STATUS`) 
                                VALUES ('$cust_id','$fname','$lname','$mi','$suffix','$sex','$email','$username','$hashed_password','$contact_no','$unit','$barangay','$picture','$bday', '$cart_id','active')";
        if ($conn->query($sql) === TRUE) {
            $sql_message = "INSERT INTO `messages`(`MESS_ID`, `LATEST_MESS_TIMESTAMP`) VALUES ('$cust_id','$currentDateTime')";
            if ($conn->query($sql_message) === TRUE) {
                $sql_message_content = "INSERT INTO `message`(`MESS_ID`, `SENDER_ID`, `MESSAGE_BODY`, `TIMESTAMP`) 
                                                        VALUES ('$cust_id','1','Hi New User','$currentDateTime')";
                if ($conn->query($sql_message_content) === TRUE) {
                    header("Location: download-app.html");
                    exit;
                }
            }
        }
    }
}

if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];
    $bday = $_POST['bday'];
    $sex = $_POST['sex'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $unit = $_POST['unit'];
    $barangay = $_POST['barangay'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cust_id = $_POST['cust_id'];
    $cart_id = $_POST['cart_id'];
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/access-denied.css">
        <link rel="stylesheet" href="css/terms-condition.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
        </style>
        <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
        <title>Terms and Condition</title>
    </head>

    <body>
        <div class="terms-condition">
            <h1><em>GOrder</em> Terms and Conditions</h1>
            <p>You acknowledge that you have read, understand, and agree to be bound by the Terms & Conditions set forth below by accessing and using GOrder. These conditions apply to the whole website as well as any emails or other correspondence you may have with the company. The price of the next transaction after a product is returned and replaced should be the same as or higher than the price total of the initial transaction. At any time, we have the right to modify the pricing structure and the resource consumption guidelines.</p>
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
                <input type="hidden" name="barangay" value="<?php echo $barangay ?>">
                <input type="hidden" name="username" value="<?php echo $username ?>">
                <input type="hidden" name="password" value="<?php echo $password ?>">
                <input type="hidden" name="cust_id" value="<?php echo $cust_id ?>">
                <input type="hidden" name="cart_id" value="<?php echo $cart_id ?>">
                <input type="submit" name="agree" class="btn btn-primary" value="Agree">
            </form>
        </div>
    </body>

    </html>

<?php
} else {
    header('Location: index.php');
    exit;
}
?>