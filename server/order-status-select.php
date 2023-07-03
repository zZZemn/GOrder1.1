<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
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
                $prescription = $order['PRESCRIPTION'];
                $pof = $order['PROOF_OF_PAYMENT'];
                $payment_type = $order['PAYMENT_TYPE'];

                $prescription_reason = $order['PRES_REJECT_REASON'];

                if ($del_type === 'Deliver') {
                    if ($order_status === 'Waiting') {
                        if ($payment_type == 'Cash') {
?>
                            <div>
                                <center>Confirm this order?</center>
                                <div>
                                    <a class="btn btn-primary order-accept" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-order">Accept</a>
                                    <a class="btn btn-danger order-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-order">Decline</a>
                                </div>
                            </div>
                            <?php
                        } else {
                            //online payment
                            if ($prescription != null && $prescription_reason != 'confirmed') {
                            ?>
                                <div>
                                    <center>Confirm this Prescription?</center>
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="accept-prescription">Confirm</a>
                                        <a class="btn btn-danger prescription-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-prescription">Decline</a>
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div>
                                    <center>Confirm this Payment?</center>
                                    <div>
                                        <a class="btn btn-primary pof-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-payment">Confirm</a>
                                        <a class="btn btn-danger pof-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-payment">Decline</a>
                                    </div>
                                </div>
<?php
                            }
                        }
                    } elseif($order_status === 'Accepted'){
                        ?>
                        <h1>
                            accepted
                        </h1>
                        <?php
                    }   
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
