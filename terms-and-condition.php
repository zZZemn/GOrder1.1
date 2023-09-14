<?php
error_reporting(0);
include('database/db.php');
include('time-date.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agree'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name'])) {
    $_SESSION['fname'] = $_POST['first_name'];
    $_SESSION['lname'] = $_POST['last_name'];
    $_SESSION['mi'] = $_POST['mi'];
    $_SESSION['suffix'] = $_POST['suffix'];
    $_SESSION['bday'] = $_POST['birthday'];
    $_SESSION['sex'] = $_POST['sex'];
    $_SESSION['contact_no'] = $_POST['contact'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['unit'] = $_POST['unit'];
    $_SESSION['barangay'] = $_POST['barangay'];
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];

    $cust_id = rand(00000000, 99999999);
    $cust_sql = $conn->query("SELECT * FROM `customer_user` WHERE `CUST_ID` = '$cust_id'");
    while ($cust_sql->num_rows > 0) {
        $cust_id = rand(00000000, 99999999);
        $cust_sql = $conn->query("SELECT * FROM `customer_user` WHERE `CUST_ID` = '$cust_id'");
    }

    $cart_id = rand(0000000, 9999999);
    $cart_sql = $conn->query("SELECT * FROM `cart` WHERE `CART_ID` = '$cart_id'");
    while ($cart_sql->num_rows > 0) {
        $cart_id = rand(0000000, 9999999);
        $cart_sql = $conn->query("SELECT * FROM `cart` WHERE `CART_ID` = '$cart_id'");
    }

    $_SESSION['cust_id'] = $cust_id;
    $_SESSION['cart_id'] = $cart_id;

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
                <input type="hidden" name="fname" value="<?= $_SESSION['fname'] ?>">
                <input type="hidden" name="lname" value="<?= $_SESSION['lname'] ?>">
                <input type="hidden" name="mi" value="<?= $_SESSION['mi'] ?>">
                <input type="hidden" name="suffix" value="<?= $_SESSION['suffix'] ?>">
                <input type="hidden" name="bday" value="<?= $_SESSION['bday'] ?>">
                <input type="hidden" name="sex" value="<?= $_SESSION['sex'] ?>">
                <input type="hidden" name="contact_no" value="<?= $_SESSION['contact_no'] ?>">
                <input type="hidden" name="email" value="<?= $_SESSION['email'] ?>">
                <input type="hidden" name="unit" value="<?= $_SESSION['unit'] ?>">
                <input type="hidden" name="barangay" value="<?= $_SESSION['barangay'] ?>">
                <input type="hidden" name="username" value="<?= $_SESSION['username'] ?>">
                <input type="hidden" name="password" value="<?= $_SESSION['password'] ?>">
                <input type="hidden" name="cust_id" value="<?= $_SESSION['cust_id'] ?>">
                <input type="hidden" name="cart_id" value="<?= $_SESSION['cart_id'] ?>">
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