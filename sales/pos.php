<?php
error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
} else {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/access-denied.css">
    <link rel="stylesheet" href="../css/message.css">
    <link rel="stylesheet" href="../css/pos-nav.css">
    <link rel="stylesheet" href="../css/pos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | POS</title>
</head>

<body>
    <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") : ?>
        <nav class="top-nav bg-dark">

            <i class="fa-solid fa-bars menu"></i>

            <img class="logo" src="../img/ggd-text-logo.png" alt="Golden Gate Drugstore">

            <div class="top-navigations">
                <a href="pos.php" class="top-navigations-active">POS</a>
                <a href="orders.php">Orders</a>
                <a href="sales.php">Sales</a>
                <a href="return.php">Return</a>
            </div>

            <ul>
                <li class="message-dropdown dropdown">
                    <a>
                        <i class="fa-solid fa-message"></i>
                    </a>
                </li>
                <div class="message-dropdown-container">
                    <?php
                    $messages = "SELECT * FROM messages ORDER BY LATEST_MESS_TIMESTAMP DESC";
                    $messages_result = $conn->query($messages);
                    if ($messages_result->num_rows > 0) {
                    ?>
                        <?php

                        while ($messages_row = $messages_result->fetch_assoc()) {
                            $mess_id = $messages_row['MESS_ID'];
                            $message = "SELECT * FROM message WHERE MESS_ID = $mess_id";
                            $message_result = $conn->query($message);

                            if ($message_result->num_rows > 0) {
                                $latest_message = "SELECT * FROM message WHERE MESS_ID = $mess_id AND TIMESTAMP = ( SELECT MAX(TIMESTAMP) FROM message WHERE MESS_ID = $mess_id)";
                                $latest_message_result = $conn->query($latest_message);
                                $latest_message_row = $latest_message_result->fetch_assoc();

                                $message_id = $latest_message_row['MESS_ID'];

                                $customer = "SELECT * FROM customer_user WHERE CUST_ID = $message_id";
                                $customer_result = $conn->query($customer);
                                $customer_row = $customer_result->fetch_assoc();

                        ?>
                                <a href="#" class="dropdown-message <?php echo $customer_row['CUST_ID'] ?>">
                                    <div class="from">
                                        <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>">
                                        <h3><?php echo $customer_row['FIRST_NAME'] . " " . $customer_row['LAST_NAME']; ?></h3>
                                    </div>
                                    <p><?php echo $latest_message_row['MESSAGE_BODY'] ?></p>
                                    <article><?php echo $latest_message_row['TIMESTAMP'] ?></article>
                                </a>
                                <hr>
                        <?php
                            }
                        }
                    } else {

                        ?>
                        <center class="text-light">No message found</center>
                    <?php
                    }
                    ?>
                </div>

                <li class="notification-dropdown dropdown">
                    <i class="fa-solid fa-bell"></i>
                    <div id="notifications-count">

                    </div>

                    <?php
                    ?>
                </li>
                <div class="notification-dropdown-container" id="notification-dropdown-container">

                </div>

                <li class="avatar-dropdown dropdown">
                    <?php
                    if ($emp['EMP_TYPE'] === 'PA') {
                    ?>
                        <em class="admin-em" style="font-size: 11px;">
                            Pharmacy Assistant
                        </em>
                    <?php
                    } else {
                    ?>
                        <em class="admin-em">
                            <?php echo $emp['EMP_TYPE']; ?>
                        </em>
                    <?php
                    }
                    ?>

                    <img src="../img/userprofile/<?php echo $emp['PICTURE'] ?>">
                </li>
                <div class="avatar-dropdown-container">
                    <a href="
                    <?php echo $emp['EMP_TYPE'] === 'Admin' ? '../admin/avatar-profile.php' : ($emp['EMP_TYPE'] === 'PA' ? '#' : '#') ?>"><i class="fa-solid fa-user"></i>Profile</a>
                    <hr>
                    <a href="<?php echo $emp['EMP_TYPE'] === 'Admin' ? '../admin/avatar-settings.php' : ($emp['EMP_TYPE'] === 'PA' ? '#' : '#') ?>"><i class="fa-solid fa-gear"></i>Settings</a>
                    <hr>
                    <?php
                    if ($emp['EMP_TYPE'] === 'Admin') {
                    ?>
                        <a href="../admin/dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        <hr>
                    <?php
                    }
                    ?>
                    <a href="../process/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
                </div>
            </ul>


        </nav>

        <div class="alert alert-no-qty-left bg-warning">
            No stock available
        </div>

        <div class="alert alert-inv-qty-input bg-warning">
            You are unable to input a quantity greater than the available stock.
        </div>

        <div class="alert bg-warning enter-fname">
            <p class="">Please enter customers first name.</p>
        </div>
        <div class="alert bg-warning enter-lname">
            <p class="">Please enter customers last name.</p>
        </div>
        <div class="alert bg-warning enter-bgy">
            <p class="">Please put customers address.</p>
        </div>
        <div class="alert bg-warning enter-unit">
            <p class="">Please enter customers unit/street/village.</p>
        </div>
        <div class="alert bg-warning enter-birthday">
            <p class="">Please enter customers birthdate.</p>
        </div>
        <div class="alert bg-warning invalid-birthdate">
            <p class="">Please enter valid birthdate.</p>
        </div>
        <div class="alert bg-success text-light cust-added">
            <p class="">Customer Added.</p>
        </div>
        <div class="alert bg-danger cust-not-added">
            <p class="">Customer Not Added.</p>
        </div>
        <div class="alert bg-danger all-input-empty text-light">
            <p class="">All inputs are empty.</p>
        </div>
        <div class="alert bg-danger insert-money-error text-light">
            <p class="">Something Went Wrong :<< /p>
        </div>
        <div class="alert bg-success insert-money-success text-light">
            <p class="">Adding Money Success!</p>
        </div>

        <div class="pos-container hide-me-final">
            <form class="pos-orders-container" id="order_list">
                <center class="only-print">
                    <p id="ggd"></p>
                    <p id="ggd-add"></p>
                    <p id="date-time-print"></p>
                </center>
                <table class="table table-striped" id="receipt-table">
                    <thead>
                        <tr>
                            <th class="product-receipt-header">Product</th>
                            <th>Price</th>
                            <th class="th-qty"><span>Quantity</span></th>
                            <th class="th-amt"><span>Amount</span></th>
                            <th class="remove-when-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="computation-container-receipt" class="only-print">
                    <div id="receipt-subtotal"></div>
                    <div id="receipt-vat"></div>
                    <div id="receipt-discount"></div>
                    <div id="receipt-total"></div>
                    <div id="receipt-payment"></div>
                    <div id="receipt-change"></div>
                </div>

                <?php

                $vat_rate_sql = "SELECT * FROM tax WHERE TAX_ID = '1'";
                $vat_rate_result = $conn->query($vat_rate_sql);
                $vat = $vat_rate_result->fetch_assoc();
                $vatRate = $vat['TAX_PERCENTAGE'];

                ?>

                <input type="hidden" name="vatRate" id="vatRate" value="<?php echo $vatRate ?>">

                <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp['EMP_ID'] ?>">

                <div class="computation remove-when-print">
                    <div class="top">
                        <div class="input">
                            <input type="number" name="subtotal" id="subtotal" class="form-control" readonly required value="0.00">
                            <label for="subtotal">Subtotal</label>
                        </div>
                        <div class="input">
                            <input type="number" name="total" id="total" class="form-control" readonly required value="0.00">
                            <label for="total">Total</label>
                        </div>
                        <div class="input">
                            <input type="number" name="payment" id="payment" class="form-control text-primary" required>
                            <label for="payment">Payment</label>
                        </div>

                        <div class="input">
                            <select name="cust_type" id="cust_type" class="form-control cust_type" required>
                                <option value="0">Regular</option>
                                <?php
                                $discount_result = $conn->query("SELECT * FROM discount WHERE DISCOUNT_STATUS = 'active'");
                                if ($discount_result->num_rows > 0) {
                                    while ($discount_row = $discount_result->fetch_assoc()) {
                                ?>
                                        <option value="<?php echo $discount_row['DISCOUNT_PERCENTAGE'] ?>"><?php echo $discount_row['DISCOUNT_NAME'] ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="cust_type">Customer Type</label>
                        </div>

                        <input type="submit" name="save_print" id="save_print" class="btn btn-primary save_print" value="Pay" disabled>
                    </div>

                    <div class="bot">
                        <div class="input">
                            <input type="number" name="vat" id="vat" class="form-control" readonly required value="0.00">
                            <label for="vat">VAT</label>
                        </div>

                        <div class="input">
                            <input type="number" name="discount" id="discount" class="form-control" readonly required value="0.00">
                            <label for="discount">Discount</label>
                        </div>

                        <div class="input">
                            <input type="number" name="change" id="change" class="form-control text-success" readonly required min="0" value="0.00" oninput="validity.valid||(value='0');">
                            <label for="Change">Change</label>
                        </div>

                        <div class="input">
                            <input type="number" name="cust_id" id="cust_id" class="cust_id form-control" placeholder="Optional">
                            <label for="cust_id">Customer ID</label>
                        </div>


                        <input type="submit" name="reset" id="reset" class="btn btn-danger save_print" value="Reset">
                    </div>
                </div>
            </form>

            <div class="pos-select-item-container">
                <form class="search-products">
                    <input type="text" class="form-control" name="query" id="search_products" placeholder="Scan / Search Products..." autofocus>
                </form>
                <div class="search-results" id="search_results">

                </div>
            </div>

        </div>

        <!-- End -->

        <div class="main">
            <form id="frm-add-money" class="frm-add-money hide-me-final">
                <button type="button" id="close-add-money"><i class="fa-solid fa-xmark"></i></button>
                <center>Add Money</center>
                <div class="inputs-money-container">
                    <div class="input-container">
                        <input type="number" class="form-control" id="onek" name="onek" placeholder="How many ₱ 1000?">
                        <label for="onek">1000</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="fiveHundred" name="fiveHundred" placeholder="How many ₱ 500?">
                        <label for="fiveHundred">500</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="twoHundred" name="twoHundred" placeholder="How many ₱ 200?">
                        <label for="twoHundred">200</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="oneHundred" name="oneHundred" placeholder="How many ₱ 100?">
                        <label for="oneHundred">100</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="fifty" name="fifty" placeholder="How many ₱ 50?">
                        <label for="fifty">50</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="twenty" name="twenty" placeholder="How many ₱ 20?">
                        <label for="twenty">20</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="ten" name="ten" placeholder="How many ₱ 10?">
                        <label for="ten">10</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="five" name="five" placeholder="How many ₱ 5?">
                        <label for="five">5</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="one" name="one" placeholder="How many ₱ 1?">
                        <label for="one">1</label>
                    </div>
                    <div class="input-container">
                        <input type="number" class="form-control" id="cents25" name="cents25" placeholder="How many ¢ 25?">
                        <label for="cents25">25</label>
                    </div>
                </div>

                <div class="form-control add-money-btns">
                    <button type="submit" class="btn btn-primary" data-type="Add">Count</button>
                    <button type="submit" class="btn btn-primary" data-type="Shift">Shift</button>
                    <button type="submit" class="btn btn-primary" data-type="End">End of the day</button>
                </div>
            </form>

            <form id="frm-add-cust" class="frm-add-cust hide-me-final">
                <a href="#" id="close-frm-add-cust"><i class="fa-solid fa-xmark"></i></a>
                <center id="add-cust-title" class="add-cust-title">Add Customer</center>
                <div class="first-div">
                    <input type="hidden" value="" id="cust_id_hidden">
                    <div class="cust-details">
                        <div class="cust-details-f-row">
                            <div class="input-container">
                                <input type="text" class="form-control" id="fname" value="" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');">
                                <label>First Name</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="lname" value="" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');">
                                <label>Last Name</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="mi" maxlength="3" value="" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');">
                                <label>MI</label>
                            </div>
                            <div class="input-container">
                                <select id="suffix" class="form-control">
                                    <option value=""></option>
                                    <option value="Sr">Sr</option>
                                    <option value="Jr">Jr</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                                <label>Suffix</label>
                            </div>
                        </div>
                        <div class="cust-details-s-row">
                            <div class="input-container">
                                <select id="sex" class="form-control">
                                    <option value="m">Male</option>
                                    <option value="f">Female</option>
                                </select>
                                <label>Sex</label>
                            </div>
                            <div class="input-container">
                                <input type="date" id="birthday" class="form-control" required>
                                <label>Birthday</label>
                            </div>
                            <div class="input-container">
                                <select id="discount-type" class="form-control">
                                    <option value=""></option>
                                    <?php
                                    $discount_sql = "SELECT DISCOUNT_ID, DISCOUNT_NAME FROM discount WHERE DISCOUNT_STATUS = 'active'";
                                    if ($discount_result = $conn->query($discount_sql)) {
                                        if ($discount_result->num_rows > 0) {
                                            while ($discount = $discount_result->fetch_assoc()) {
                                    ?>
                                                <option value="<?php echo $discount['DISCOUNT_ID'] ?>"><?php echo $discount['DISCOUNT_NAME'] ?></option>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Customer Type</label>
                            </div>
                            <div class="input-container">
                                <input type="text" id="contact-no" class="form-control" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                <label>Contact No.</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="second-div">
                    <div class="region-province">
                        <div class="input-container">
                            <select id="region" class="form-control">
                                <?php
                                $region_sql = "SELECT REGION_ID, REGION FROM region";
                                if ($region_result = $conn->query($region_sql)) {
                                    if ($region_result->num_rows > 0) {
                                        while ($region = $region_result->fetch_assoc()) {
                                ?>
                                            <option value="<?php echo $region['REGION_ID'] ?>"><?php echo $region['REGION'] ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <label>Region</label>
                        </div>
                        <div class="input-container">
                            <select id="province" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Province</label>
                        </div>
                    </div>
                    <div class="municipality-barangay">
                        <div class="input-container">
                            <select id="municipality" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Municipality</label>
                        </div>
                        <div class="input-container">
                            <select id="barangay" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Barangay</label>
                        </div>
                    </div>
                    <div class="unit-st-container">
                        <div class="input-container">
                            <input type="text" id="unit" class="form-control">
                            <label>Unit No. / Street / Village</label>
                        </div>
                    </div>
                </div>
                <div class="third-div">
                    <div class="third-div-s-row">
                        <a href="#" id="btn-cancel" class="btn btn-dark">Cancel</a>
                        <input type="submit" id="btn-submit" class="btn btn-primary" value="Save">
                    </div>
                </div>
            </form>

            <div class="pos-add-buttons-container hide-me-final">
                <a href="#" id="btn-save-money" class="btn btn-primary"><i class="fa-solid fa-plus fa-beat"></i> Save Money</a>
                <a href="#" id="btn-add-customer" class="btn btn-primary"><i class="fa-solid fa-plus fa-beat"></i> Add Customer</a>
            </div>


            <!-- Receipt -->
            <div class="receipt-table-final">
                <center>
                    <article>Golden Gate Drugstore</article>
                    <article>Patubig, Marilao, Bulacan</article>
                    <!-- <article>TEL NO : 09123456789</article> -->
                    <article>------------------------</article>
                </center>
                <div class="products-receipt-container-main" id="receiptProductsMainContainer">
                    <!-- <div class="receipt-items-container">
                        <article class="item-name">Advil Liquid Gel</article>
                        <article>1</article>
                        <article>12</article>
                    </div> -->
                </div>
                <center>
                    <article>------------------------</article>
                </center>
                <div class="receipt-calculate-container">
                    <article>TOTAL : </article>
                    <article id="finalReceiptTotal">10.00</article>
                </div>
                <div class="receipt-calculate-container">
                    <article>CASH : </article>
                    <article id="finalReceiptCash">10.00</article>
                </div>
                <div class="receipt-calculate-container">
                    <article>CHANGE : </article>
                    <article id="finalReceiptChange">0.00</article>
                </div>
                <center>
                    <article>------------------------</article>
                </center>
                <div class="receipt-calculate-container">
                    <article>Discount : </article>
                    <article id="finalReceiptDiscount">0.00</article>
                </div>
                <div class="receipt-calculate-container">
                    <article>VAT : </article>
                    <article id="finalReceiptVat">0.00</article>
                </div>
                <center>
                    <article>------------------------</article>
                </center>
                <div class="process-by-container-date-time-or#">
                    <article>PROCESS BY: <span id="finalReceiptProcessBy"></span></article>
                    <article>DATE: <span id="finalReceiptDate"></span></article>
                    <article>TIME: <span id="finalReceiptTime"></span></article>
                    <article>OR#: <span id="finalReceiptOrNo"></span></article>
                </div>
                <center>
                    <article>===================</article>
                </center>
            </div>
            <!-- End Receipt -->



            <div class="message-container">
                <?php
                $messages = "SELECT * FROM messages ORDER BY LATEST_MESS_TIMESTAMP DESC";
                $messages_result = $conn->query($messages);
                if ($messages_result->num_rows > 0) {
                    while ($messages_row = $messages_result->fetch_assoc()) {
                        $mess_id = $messages_row['MESS_ID'];

                        $customer = "SELECT * FROM customer_user WHERE CUST_ID = $mess_id";
                        $customer_result = $conn->query($customer);
                        $customer_row = $customer_result->fetch_assoc();
                ?>

                        <div class="message-content <?php echo "message" . $customer_row['CUST_ID'] . "message" ?>">
                            <div class="message-header">
                                <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>" alt="avatar">
                                <p><?php echo $customer_row['FIRST_NAME'] . " " . $customer_row['LAST_NAME'] ?></p>
                                <button class="close-message"><i class="fa-solid fa-circle-xmark"></i></button>
                            </div>
                            <div id="message-container" class="message-text">
                                <?php

                                $messages_content = "SELECT * FROM message WHERE MESS_ID = $mess_id ORDER BY TIMESTAMP ASC";
                                $messages_content_result = $conn->query($messages_content);

                                if ($messages_content_result->num_rows > 0) {
                                    while ($messages_content_row = $messages_content_result->fetch_assoc()) {
                                        if ($messages_content_row['MESS_ID'] === $messages_content_row['SENDER_ID']) {
                                            $messageFrom = "SELECT * FROM customer_user WHERE CUST_ID = {$messages_content_row['MESS_ID']}";
                                            $messageFrom_result = $conn->query($messageFrom);
                                            $senderCustomer = $messageFrom_result->fetch_assoc();

                                            $sender = $senderCustomer['FIRST_NAME'] . " " . $senderCustomer['LAST_NAME'];
                                        } else {
                                            $sender = "GOrder";
                                        }
                                ?>
                                        <div>
                                            <article><?php echo $sender ?></article>
                                            <p><?php echo $messages_content_row['MESSAGE_BODY'] ?></p>
                                        </div>

                                <?php

                                    }
                                }

                                ?>
                            </div>
                            <form class="send-message send-message-form" id="send-message">
                                <input type="hidden" value="<?php echo $emp['EMP_ID'] ?>" name="sender_id">
                                <input type="hidden" value="<?php echo $mess_id ?>" name="message_id">
                                <input type="text" name="message" class="textfield">
                                <button type="submit" name="send" class="send"><i class="fa-solid fa-paper-plane"></i></button>
                            </form>
                        </div>

                <?php
                    }
                }
                ?>
            </div>
        </div>

        <input type="hidden" id="personLoggedIn" value="<?= $emp['FIRST_NAME'] . " " . $emp["MIDDLE_INITIAL"] . " " . $emp['LAST_NAME'] ?>">
        <p class="emptype-name remove-when-print">Printed By: <?php echo $emp['FIRST_NAME'] . " " . $emp["MIDDLE_INITIAL"] . " " . $emp['LAST_NAME'] ?></p>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
        <script src="../js/side-nav-dropdown.js"></script>
        <script src="../js/nav-avatar-dropdown.js"></script>
        <script src="../js/nav-notif-dropdown.js"></script>
        <script src="../js/nav-message-dropdown.js"></script>
        <script src="../js/message.js"></script>
        <script src="../js/mess-send.js"></script>
        <script src="../js/mess-scroll.js"></script>
        <script src="../js/pos-product-search.js"></script>
        <script src="../js/notifications.js"></script>


    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>