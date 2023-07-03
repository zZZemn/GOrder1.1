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
            $transactionID = $_GET['id'];
            $orderDetails_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transactionID'";
            $orderDetails_result = $conn->query($orderDetails_sql);
            if ($orderDetails_result->num_rows > 0) {
                $order = $orderDetails_result->fetch_assoc();
                $order_prescription = $order['PRESCRIPTION'];
                $order_pof = $order['PROOF_OF_PAYMENT'];
                $order_status = $order['STATUS'];
                $del_type = $order['DELIVERY_TYPE'];
                $cust_id = $order['CUST_ID'];
                $cur_rider_id = $order['RIDER_ID'];
                $bgy_id = $order['BARANGAY_ID'];
                $payment_type = $order['PAYMENT_TYPE'];

                $subtotal = $order['SUBTOTAL'];
                $val = $order['VAT'];
                $discount = $order['DISCOUNT'];
                $total = $order['TOTAL'];
                $payment = $order['PAYMENT'];
                $change = $order['CHANGE'];

                $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                $bgy_result = $conn->query($bgy_sql);
                $bgy = $bgy_result->fetch_assoc();
                $del_fee = $bgy['DELIVERY_FEE'];
?>
                <table class="table table-striped" id="orders-product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Selling Price</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orderProducts_sql = "SELECT * FROM order_details WHERE TRANSACTION_ID = '$transactionID'";
                        $orderProducts_result = $conn->query($orderProducts_sql);
                        if ($orderProducts_result->num_rows > 0) {
                            while ($order_row = $orderProducts_result->fetch_assoc()) {
                                $product_id = $order_row['PRODUCT_ID'];
                                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                                $product_result = $conn->query($product_sql);
                                $product = $product_result->fetch_assoc();
                        ?>
                                <tr id="products-details">
                                    <input type="hidden" id="product_id" value="<?php echo $product_id ?>">
                                    <input type="hidden" id="qty" value="<?php echo $order_row['QTY'] ?>">
                                    <input type="hidden" id="amount" value="<?php echo $order_row['AMOUNT'] ?>">
                                    <td><?php echo $product['PRODUCT_NAME'] ?></td>
                                    <td><?php echo $product['SELLING_PRICE'] ?></td>
                                    <td><?php echo $order_row['QTY'] ?></td>
                                    <td><?php echo $order_row['AMOUNT'] ?></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <th colspan="3">Subtotal</th>
                            <th><?php echo $order['SUBTOTAL'] ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">VAT</th>
                            <th><?php echo $order['VAT'] ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">Discount</th>
                            <th><?php echo $order['DISCOUNT'] ?></th>
                        </tr>
                        <?php
                        if ($del_type === 'Deliver') {
                        ?>
                            <tr>
                                <th colspan="3">Delivery Fee</th>
                                <th><?php echo $del_fee ?></th>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th colspan="3">Total</th>
                            <th><?php echo $order['TOTAL'] ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">Payment</th>
                            <th><?php echo ($order['PAYMENT'] != 0) ? $order['PAYMENT'] : '<p class="text-danger">Not Paid</p>' ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">Change</th>
                            <th><?php echo ($order['PAYMENT'] != 0) ? $order['CHANGE'] : '<p class="text-danger">Not Paid</p>' ?></th>
                        </tr>
                        <?php if ($del_type === 'Pick Up' && $order_status != 'Picked Up' && $payment_type === 'Cash') {
                        ?>
                            <tr>
                                <th colspan="3" class="text-success add-payment-text">Add Payment</th>
                                <th class="payment-input-th">
                                    <div class="payment-input-div">
                                        <input type="hidden" id="total_hidden" value="<?php echo $order['TOTAL'] ?>">
                                        <input type="number" id="payment" class="form-control">
                                        <a class="btn btn-success" id="payment_submit">Pay</a>
                                    </div>
                                </th>
                            </tr>
                            <?php
                        }

                        if ($payment_type === 'Cash') {
                            if ($order_prescription != null) {
                            ?>
                                <tr>
                                    <th colspan="4">
                                        <center>Prescription</center>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <center>
                                            <img src="../img/prescriptions/<?php echo $order_prescription ?>">
                                    </td>
                                    </center>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <?php echo ($order_prescription != null) ? '<th colspan="2"><center>Prescription</center></th>' : '' ?>
                                <th colspan="4">
                                    <center>
                                        Proof Of Payment
                                    </center>
                                </th>
                            </tr>
                            <tr>
                            <tr>
                            <?php echo ($order_prescription != null) ? '<td colspan="2"><center><img class="user-upload" src="../img/prescriptions/'.$order_prescription.'"</center></td>' : '' ?>
                                <td colspan="4">
                                    <center>
                                        <?php
                                        echo ($order_pof == null) ? '<p colspan="2">Proof Of Payment Not Uploaded Yet.</p>' : '<img class="user-upload" src="../img/pofs/' . $order_pof . '">';
                                        ?>
                                    </center>
                                </td>
                            </tr>
                            </tr>
                        <?php
                        }
                        ?>
                        <input type="hidden" id="payment_type" value="<?php echo $payment_type ?>">
                        <input type="hidden" id="cust_id" value="<?php echo $cust_id ?>">
                        <input type="hidden" id="subtotal" value="<?php echo $subtotal ?>">
                        <input type="hidden" id="vat" value="<?php echo $val ?>">
                        <input type="hidden" id="discount" value="<?php echo $discount ?>">
                        <input type="hidden" id="total" value="<?php echo $total ?>">
                        <input type="hidden" id="payment" value="<?php echo $payment ?>">
                        <input type="hidden" id="change" value="<?php echo $change ?>">
                    </tbody>
                </table>
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
?>