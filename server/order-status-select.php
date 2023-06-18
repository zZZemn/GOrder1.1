<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $transactionID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
            $orderDetails_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transactionID'";
            $orderDetails_result = $conn->query($orderDetails_sql);
            if ($orderDetails_result->num_rows > 0) {
                $order = $orderDetails_result->fetch_assoc();
                $order_status = $order['STATUS'];
                $del_type = $order['DELIVERY_TYPE'];
                $cust_id = $order['CUST_ID'];
                $cur_rider_id = $order['RIDER_ID'];
?>
                <?php if ($del_type === 'Deliver') { ?>
                    <div class="order-input-container">
                        <select class="form-control" id="update-order-status" <?php echo ($order_status === 'Delivered') ? 'disabled' : '' ?>>
                            <?php
                            if ($order_status === 'Delivered') {
                            ?>
                                <option value="Delivered">Delivered</option>
                            <?php
                            } elseif ($order_status === 'For-Delivery') {
                            ?>
                                <option value="For-Delivery" <?php echo ($order_status === 'For-Delivery') ? 'selected' : '' ?>>For Delivery</option>
                                <option value="Shipped" <?php echo ($order_status === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                            <?php
                            } elseif ($order_status === 'Shipped') {
                            ?>
                                <option value="Shipped" <?php echo ($order_status === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                            <?php
                            } else {
                            ?>
                                <option value="Waiting" <?php echo ($order_status === 'Waiting') ? 'selected' : '' ?>>Waiting</option>
                                <option value="Accepted" <?php echo ($order_status === 'Accepted') ? 'selected' : '' ?>>Accepted</option>
                                <option value="For-Delivery" <?php echo ($order_status === 'For-Delivery') ? 'selected' : '' ?>>For Delivery</option>
                                <option value="Shipped" <?php echo ($order_status === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                            <?php
                            }
                        } elseif ($del_type === 'Pick Up') {
                            ?>
                            <div class="order-input-container">
                                <select class="form-control" id="update-order-status" <?php echo ($order_status === 'Picked Up') ? 'disabled' : '' ?>>
                                    <?php
                                    if ($order_status === 'Picked Up') {
                                    ?>
                                        <option value="Picked Up">Picked Up</option>
                                    <?php
                                    } else {
                                    ?>
                                        <option value="Waiting" <?php echo ($order_status === 'Waiting') ? 'selected' : '' ?>>Waiting</option>
                                        <option value="Accepted" <?php echo ($order_status === 'Accepted') ? 'selected' : '' ?>>Accepted</option>
                                        <option value="Ready To Pick Up" <?php echo ($order_status === 'Ready To Pick Up') ? 'selected' : '' ?>>Ready To Pick Up</option>
                                <?php
                                    }
                                }
                                ?>
                                </select>
                                <label>Order Status</label>
                            </div>
                    </div>
    <?php
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
