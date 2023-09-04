<?php
error_reporting(0);
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['transaction_id'])) {
            $transactionID = filter_input(INPUT_GET, 'transaction_id', FILTER_SANITIZE_STRING);
            $orderDetails_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transactionID'";
            $orderDetails_result = $conn->query($orderDetails_sql);
            if ($orderDetails_result->num_rows > 0) {
                $order = $orderDetails_result->fetch_assoc();
                $order_status = $order['STATUS'];
                $del_type = $order['DELIVERY_TYPE'];
                $cust_id = $order['CUST_ID'];
                $cur_rider_id = $order['RIDER_ID'];

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
                    <link rel="stylesheet" href="../css/order-details.css">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
                    <title>GOrder | Order Details</title>
                </head>

                <body>

                    <div class="alert alert-payment-invalid bg-warning">
                        The payment must be greater than or equal to the total amount.
                    </div>
                    <div class="alert alert-transaction-complete bg-success">
                        Transaction Completed.
                    </div>


                    <div class="top-contents-container">
                        <div class="order-input-container">
                            <input type="text" readonly class="transaction-id form-control" id="transaction_id" value="<?php echo $order['TRANSACTION_ID'] ?>">
                            <label>Transaction ID</label>
                        </div>
                        <div class="status-container" id="status_container">

                        </div>

                        <div id="select_status_container">

                        </div>
                    </div>

                    <div class="second-container">
                        <?php
                        $user_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
                        $user_result = $conn->query($user_sql);
                        if ($user_result->num_rows > 0) {
                            $user = $user_result->fetch_assoc();
                        ?>
                            <div class="two-div-print">

                                <div class="order-input-container">
                                    <input type="text" class="form-control" readonly value="<?php echo $user['FIRST_NAME'] . " " . $user['LAST_NAME'] ?>">
                                    <label>Order By</label>
                                </div>
                                <div class="order-input-container">
                                    <input type="text" class="form-control" readonly value="<?php echo $order['DELIVERY_TYPE'] ?>">
                                    <label>Delivery Type</label>
                                </div>
                                <div class="order-input-container">
                                    <input type="text" class="form-control" readonly value="<?php echo $order['PAYMENT_TYPE'] ?>">
                                    <label>Payment Type</label>
                                </div>
                            </div>
                            <div class="two-div-print">

                                <div class="order-input-container">
                                    <input type="text" class="form-control" readonly value="<?php echo $order['DATE'] ?>">
                                    <label>Order Date</label>
                                </div>
                                <div class="order-input-container">
                                    <input type="text" class="form-control" readonly value="<?php echo date("h:i a", strtotime($order['TIME'])); ?>">
                                    <label>Order Time</label>
                                </div>
                                <?php
                                if ($del_type === 'Deliver') {
                                ?>
                                    <div class="order-input-container">
                                        <select type="text" class="form-control" id="pick-delivery-man" placeholder="Select Rider">
                                            <option value="" disabled selected style="font-size: 10px;">Select Rider</option>
                                            <?php
                                            $rider_sql = "SELECT * FROM employee WHERE EMP_TYPE = 'Rider'";
                                            $rider_result = $conn->query($rider_sql);
                                            if ($rider_result->num_rows > 0) {
                                                while ($rider = $rider_result->fetch_assoc()) {
                                                    $rider_name = $rider['FIRST_NAME'] . " " . $rider['LAST_NAME'];
                                                    $rider_id = $rider['EMP_ID'];
                                            ?>
                                                    <option value="<?php echo $rider_id ?>" <?php echo ($rider_id === $cur_rider_id) ? 'selected' : '' ?>><?php echo $rider_name ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label>Rider</label>
                                    </div>
                            </div>
                    <?php
                                }
                            } else {
                            }
                    ?>
                    </div>

                    <?php if ($del_type === 'Deliver') {
                        $unit_st = $order['UNIT_STREET'];
                        $bgy_id = $order['BARANGAY_ID'];

                        $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                        $bgy_result = $conn->query($bgy_sql);
                        $bgy = $bgy_result->fetch_assoc();

                        $barangay = $bgy['BARANGAY'];
                        $muni_id = $bgy['MUNICIPALITY_ID'];

                        $muni_sql = "SELECT * FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'";
                        $muni_result = $conn->query($muni_sql);
                        $muni = $muni_result->fetch_assoc();

                        $municipality = $muni['MUNICIPALITY'];
                        $prov_id = $muni['PROVINCE_ID'];

                        $province_sql = "SELECT * FROM province WHERE PROVINCE_ID = '$prov_id'";
                        $province_result = $conn->query($province_sql);
                        $prov = $province_result->fetch_assoc();

                        $province = $prov['PROVINCE'];
                        $reg_id = $prov['REGION_ID'];

                        $region_sql = "SELECT * FROM region WHERE REGION_ID = '$reg_id'";
                        $region_result = $conn->query($region_sql);
                        $reg = $region_result->fetch_assoc();

                        $region = $reg['REGION'];

                        $full_address = $unit_st . ", " . $barangay . ", " . $municipality . ", " . $province . ", " . $region;
                    ?>

                        <div class="third-container">
                            <div class="order-input-container">
                                <input type="text" class="form-control" readonly value="<?php echo $full_address ?>">
                                <label>Address</label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="fourt-container" id="fourt_container">

                    </div>

                    <button type="button" id="btn-print" class="btn btn-success">Print Waybill</button>

                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
                    <script src="../js/order-details-realtime.js"></script>
                </body>
<?php

            } else {
                echo "
                    <head>
                        <link rel='stylesheet' href='../css/access-denied.css'>
                    </head>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Transaction ID not found.</h5>
                    </div>";
            }
        } else {
            echo "
                    <head>
                        <link rel='stylesheet' href='../css/access-denied.css'>
                    </head>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Sorry, you are not authorized to access this page.</h5>
                    </div>";
        }
    } else {
        echo "
            <head>
                <link rel='stylesheet' href='../css/access-denied.css'>
            </head>
            <div class='access-denied'>
                <h1>Access Denied</h1>
                <h5>Sorry, you are not authorized to access this page.</h5>
            </div>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
