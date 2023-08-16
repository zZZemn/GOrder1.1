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
                $pof_reason = $order['POF_REJECT_REASON'];

                if ($del_type === 'Deliver') {
                    if ($order_status === 'Waiting') {
                        if ($payment_type == 'Cash') {
                            if ($prescription != null && $prescription_reason != 'confirmed' && $prescription_reason != 'declined') {
?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-prescription">Confirm</a>
                                        <a class="btn btn-danger prescription-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-prescription">Decline</a>
                                    </div>
                                    <label>Confirm this Prescription?</label>
                                </div>
                            <?php
                            } elseif ($prescription != null && $prescription_reason == 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Prescription Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-prescription">Confirm</a>
                                    </div>
                                    <label>Confirm this Order?</label>
                                </div>
                            <?php
                            }
                        } else {
                            //online payment
                            if ($prescription != null && $prescription_reason != 'confirmed' && $prescription_reason != 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="accept-prescription">Confirm</a>
                                        <a class="btn btn-danger prescription-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-prescription">Decline</a>
                                    </div>
                                    <label>Confirm this Prescription?</label>
                                </div>
                            <?php
                            } elseif ($pof != null && $pof_reason != 'confirmed' && $pof_reason != 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary pof-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-payment">Confirm</a>
                                        <a class="btn btn-danger pof-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-payment">Decline</a>
                                    </div>
                                    <label>Confirm this Payment?</label>
                                </div>
                            <?php
                            } elseif ($prescription != null && $prescription_reason === 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Prescription Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } elseif ($pof != null && $pof_reason === 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Payment Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Waiting For Payment">
                                    <label>Current Status</label>
                                </div>
                        <?php
                            }
                        }
                    } elseif ($order_status === 'Accepted') {
                        ?>
                        <div class="change-stat-div">
                            <a class="btn btn-primary btn-for-delivery" data-id="<?php echo $transactionID ?>" data-status="For-Delivery" data-action="for-delivery">For-Delivery</a>
                            <label>Change Status</label>
                        </div>
                    <?php
                    } elseif ($order_status === 'For-Delivery') {
                    ?>
                        <div class="change-stat-div">
                            <a class="btn btn-primary btn-for-delivery" data-id="<?php echo $transactionID ?>" data-status="Shipped" data-action="shipped">Ship</a>
                            <label>Change Status</label>
                        </div>
                    <?php
                    } elseif ($order_status === 'Shipped') {
                    ?>
                        <div class="change-stat-div">
                            <input type="text" class="form-control" readonly value="Shipped">
                            <label>Current Status</label>
                        </div>
                    <?php
                    } elseif ($order_status === 'Delivered') {
                    ?>
                        <div class="change-stat-div">
                            <input type="text" class="form-control" readonly value="Delivered">
                            <label>Current Status</label>
                        </div>
                        <?php
                    }
                } else {
                    //pick up
                    if ($order_status === 'Waiting') {
                        if ($payment_type == 'Cash') {
                            if ($prescription != null && $prescription_reason != 'confirmed' && $prescription_reason != 'declined') {
                        ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-prescription">Confirm</a>
                                        <a class="btn btn-danger prescription-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-prescription">Decline</a>
                                    </div>
                                    <label>Confirm this Prescription?</label>
                                </div>
                            <?php
                            } elseif ($prescription != null && $prescription_reason == 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Prescription Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-prescription">Confirm</a>
                                    </div>
                                    <label>Confirm this Order?</label>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                        } else {
                            //online payment
                            if ($prescription != null && $prescription_reason != 'confirmed' && $prescription_reason != 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary prescription-confirm" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="accept-prescription">Confirm</a>
                                        <a class="btn btn-danger prescription-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-prescription">Decline</a>
                                    </div>
                                    <label>Confirm this Prescription?</label>
                                </div>
                            <?php
                            } elseif ($pof != null && $pof_reason != 'confirmed' && $pof_reason != 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <div>
                                        <a class="btn btn-primary pof-confirm" data-id="<?php echo $transactionID ?>" data-status="Accepted" data-action="accept-payment">Confirm</a>
                                        <a class="btn btn-danger pof-decline" data-id="<?php echo $transactionID ?>" data-status="Waiting" data-action="decline-payment">Decline</a>
                                    </div>
                                    <label>Confirm this Payment?</label>
                                </div>
                            <?php
                            } elseif ($prescription != null && $prescription_reason == 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Prescription Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } elseif ($pof != null && $pof_reason == 'declined') {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Payment Declined">
                                    <label>Current Status</label>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="change-stat-div">
                                    <input type="text" readonly class="form-control" value="Waiting For Payment">
                                    <label>Current Status</label>
                                </div>
                        <?php
                            }
                        }
                    } elseif ($order_status === 'Accepted') {
                        ?>
                        <div class="change-stat-div">
                            <a class="btn btn-primary btn-for-delivery" data-id="<?php echo $transactionID ?>" data-status="Ready To Pick Up" data-action="rtp">Ready To Pick Up</a>
                            <label>Change Status</label>
                        </div>
                        <?php
                    } elseif ($order_status === 'Ready To Pick Up') {
                        if ($payment_type === 'Cash') {
                        ?>
                            <div class="change-stat-div">
                                <input type="text" readonly class="form-control" value="Ready To Pick Up">
                                <label>Current Status</label>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="change-stat-div">
                                <a class="btn btn-primary btn-for-delivery" data-id="<?php echo $transactionID ?>" data-status="Picked Up" data-action="picked-up">Picked Up</a>
                                <label>Change Status</label>
                            </div>
                        <?php
                        }
                    } elseif ($order_status === 'Picked Up') {
                        ?>
                        <div class="change-stat-div">
                            <input type="text" readonly class="form-control" value="Picked Up">
                            <label>Current Status</label>
                        </div>
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
