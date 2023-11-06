<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $transaction_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $sales_sql = "SELECT * FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
            $sales_result = $conn->query($sales_sql);
            if ($sales_result->num_rows > 0) {
                $sales = $sales_result->fetch_assoc();
                $sales_date = $sales['DATE'];
                $cust_id = $sales['CUST_ID'];
                $emp_id = $sales['EMP_ID'];

?>
                <input type="hidden" name="cust_id" id="cust_id" value="<?php echo $cust_id ?>">
                <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp_id ?>">
                <?php

                $emp_sql = "SELECT FIRST_NAME, LAST_NAME, MIDDLE_INITIAL FROM employee WHERE EMP_ID = '$emp_id'";
                $emp_result = $conn->query($emp_sql);
                $process_emp_name = '';
                if ($emp_result->num_rows > 0) {
                    $process_emp = $emp_result->fetch_assoc();
                    $process_emp_name = $process_emp['FIRST_NAME'] . " " . $process_emp['MIDDLE_INITIAL'] . ". " . $process_emp['LAST_NAME'];
                } else {
                    $process_emp_name = "NULL";
                }


                $cust_sql = "SELECT FIRST_NAME, LAST_NAME, MIDDLE_INITIAL FROM customer_user WHERE CUST_ID = '$cust_id'";
                $cust_result = $conn->query($cust_sql);
                $cust_name = '';
                if ($cust_result->num_rows > 0) {
                    $cust = $cust_result->fetch_assoc();
                    $cust_name = $cust['FIRST_NAME'] . " " . $cust['MIDDLE_INITIAL'] . ". " . $cust['LAST_NAME'];
                } else {
                    $cust_name = "NULL";
                }

                if ($sales_date >= $sevenDaysAgo) {
                    $sales_details_sql = "SELECT * FROM sales_details WHERE TRANSACTION_ID = '$transaction_id'";
                    $sales_details_result = $conn->query($sales_details_sql);
                    if ($sales_details_result->num_rows > 0) {
                ?>
                        <table class="">
                            <thead>
                                <tr class="return-products-tr">
                                    <th colspan="5" class="bg-dark text-light">
                                        PRODUCT RETURN
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="5"><label>Employee</label><?php echo $process_emp_name ?></th>
                                </tr>
                                <tr>
                                    <th><label>Transaction ID</label><?php echo $sales['TRANSACTION_ID'] ?></th>
                                    <th><label>Transaction Type</label><?php echo $sales['TRANSACTION_TYPE'] ?></th>
                                    <th><label>Payment Type</label><?php echo $sales['PAYMENT_TYPE'] ?></th>
                                    <th><label>Customer Name</label><?php echo $cust_name ?></th>
                                    <th><label>Date and Time</label><?php echo $sales['DATE'] . " - " . date("h:i A", strtotime($sales['TIME'])) ?></th>
                                </tr>
                                <tr class="sales-details-tr">
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Expiration Date</th>
                                    <th>Return Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($salesD_row = $sales_details_result->fetch_array()) {
                                    $prod_id = $salesD_row['PRODUCT_ID'];
                                    $inv_id = $salesD_row['INV_ID'];

                                    $product_sql = "SELECT PRODUCT_NAME FROM products WHERE PRODUCT_ID = '$prod_id'";
                                    $product_result = $conn->query($product_sql);
                                    $product = $product_result->fetch_assoc();
                                    $product_name = $product['PRODUCT_NAME'];

                                    $inv_sql = "SELECT EXP_DATE FROM inventory WHERE INV_ID = '$inv_id'";
                                    $inv_result = $conn->query($inv_sql);
                                    $inventory = $inv_result->fetch_assoc();
                                    $exp_date = $inventory['EXP_DATE'];

                                    $amount = $salesD_row['AMOUNT'];
                                    $qty = $salesD_row['QUANTITY'];

                                    $price_pc = 0;
                                    if ($qty != 0 && $amount != 0) {
                                        $price_pc =  $amount / $qty;
                                    }
                                ?>
                                    <input type="hidden" id="transaction_id" value="<?php echo $sales['TRANSACTION_ID'] ?>">
                                    <tr>
                                        <td><?php echo $product_name ?></td>
                                        <td class="text-center"><?php echo $salesD_row['QUANTITY'] ?></td>
                                        <td class="pl"><?php echo $salesD_row['AMOUNT'] ?></td>
                                        <td class="text-center"><?php echo $exp_date ?></td>
                                        <td class="text-center qty-td">
                                            <?php
                                            $return_check_sql = "SELECT * FROM `return` WHERE TRANSACTION_ID = '$transaction_id'";
                                            $return_check_result = $conn->query($return_check_sql);
                                            ?>
                                            <div class="qty-container-div">
                                                <input type="hidden" id="price" value="<?php echo $price_pc ?>" <?php echo ($return_check_result->num_rows > 0) ? 'disabled' : '' ?>>
                                                <input type="number" class="form-control" name="rtn_quantity" id="<?php echo $salesD_row['INV_ID'] ?>" placeholder="Enter Quantity" min="0" max="<?php echo htmlspecialchars($salesD_row['QUANTITY']) ?>" oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;" <?php echo ($return_check_result->num_rows > 0) ? 'disabled' : '' ?>>
                                                <label class="alert alert-when-invalid-qty text-danger">Invalid inputs will not be save.</label>
                                                <label class="alert alert-when-reach-maxlevel text-danger">Maximum level.</label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="btn-add-return-td">
                                        <select class="return-reason form-control" id="return_reason">
                                            <option disabled selected>Select Reason</option>
                                            <option value="Wrong Product">Wrong Product</option>
                                            <option value="Product Defective">Product Defective</option>
                                            <option value="Product Expired">Product Expired</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="btn-add-return-td"><a href="#" id="submit_return" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal"><i class="fa-solid fa-plus"></i> Add Return</a></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Subtotal</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['SUBTOTAL'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">VAT</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['VAT'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Discount</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['DISCOUNT'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['TOTAL'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Payment</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['PAYMENT'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Change</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['CHANGE'] ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Updated Total</td>
                                    <td>:</td>
                                    <td colspan="" class="pl-b"><?php echo $sales['UPDATED_TOTAL'] ?></td>
                                </tr>
                                <tr class="return-details-center-tr">
                                    <th colspan="5" class="bg-dark text-light">
                                        <center>Return Details</center>
                                    </th>
                                </tr>
                                <?php
                                if ($return_check_result->num_rows > 0) {
                                    $return = $return_check_result->fetch_assoc();
                                    $return_id = $return['RETURN_ID'];
                                ?>
                                    <input type="hidden" id="return_id" name="return_id" value="<?php echo $return_id ?>">
                                    <?php
                                    ?>
                                    <tr class="return-details-header-tr">
                                        <th colspan="3">
                                            <label>Date</label>
                                            <?php echo $return['RETURN_DATE'] ?>
                                        </th>
                                        <th colspan="2">
                                            <label>Return Amount</label>
                                            <?php echo $return['RETURN_AMOUNT'] ?>
                                        </th>
                                    </tr>
                                    <?php
                                    $return_items_sql = "SELECT * FROM return_items WHERE RETURN_ID = '$return_id'";
                                    $return_items_result = $conn->query($return_items_sql);
                                    if ($return_check_result->num_rows > 0) {
                                    ?>
                                        <tr class="return-items-tr">
                                            <th colspan="2">Inventory ID</th>
                                            <th colspan="2">Product Name</th>
                                            <th>Returned Quantity</th>
                                        </tr>
                                        <?php
                                        while ($row = $return_items_result->fetch_assoc()) {
                                            $inv_id = $row['INV_ID'];
                                            $inv_sql = "SELECT PRODUCT_ID FROM inventory WHERE INV_ID = '$inv_id'";
                                            $inv_result = $conn->query($inv_sql);
                                            if ($inv_result->num_rows > 0) {
                                                $inv = $inv_result->fetch_assoc();
                                                $product_id = $inv['PRODUCT_ID'];

                                                $product_sql = "SELECT PRODUCT_NAME FROM products WHERE PRODUCT_ID = '$product_id'";
                                                $product_result = $conn->query($product_sql);
                                                if ($product_result->num_rows > 0) {
                                                    $product = $product_result->fetch_assoc();
                                                    $product_name = $product['PRODUCT_NAME'];
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td colspan="2"><?php echo $row['INV_ID'] ?></td>
                                                <td colspan="2"><?php echo $product_name ?></td>
                                                <td class="text-center"><?php echo $row['QTY'] ?></td>
                                            </tr>
                                            <?php
                                        }

                                        $return_sql = "SELECT REPLACE_ID FROM `return` WHERE RETURN_ID = '$return_id'";
                                        $return_result = $conn->query($return_sql);
                                        if ($return_result->num_rows > 0) {
                                            $return2 = $return_result->fetch_assoc();
                                            $replace_id = $return2['REPLACE_ID'];

                                            if ($replace_id != null) {
                                                $replace_data_sql = "SELECT * FROM sales_details WHERE TRANSACTION_ID = '$replace_id'";
                                                $replace_data_result = $conn->query($replace_data_sql);
                                                if ($replace_data_result->num_rows > 0) {
                                            ?>
                                                    <tr class="return-details-center-tr">
                                                        <th colspan="5" class="bg-dark text-light">
                                                            <center>Replaced Items</center>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2">Product Name</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    <?php
                                                    while ($replace = $replace_data_result->fetch_assoc()) {
                                                        $product_id = $replace['PRODUCT_ID'];
                                                        $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                                                        $product_result = $conn->query($product_sql);
                                                        $product = $product_result->fetch_assoc();
                                                    ?>
                                                        <tr>
                                                            <td colspan="2">
                                                                <?php echo $product['PRODUCT_NAME'];
                                                                echo ($product['MG'] > 0) ? ' ' . $product['MG'] . 'mg' : '';
                                                                echo ($product['G'] > 0) ? ' ' . $product['G'] . 'g' : '';
                                                                echo ($product['ML'] > 0) ? ' ' . $product['ML'] . 'ml' : '';
                                                                ?></td>
                                                            <td><?php echo $product['SELLING_PRICE'] ?></td>
                                                            <td><?php echo $replace['QUANTITY'] ?></td>
                                                            <td><?php echo $replace['AMOUNT'] ?></td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                            } else {
                                                ?>
                                                <tr class="return-details-center-tr">
                                                    <th colspan="5" class="bg-dark text-light">
                                                        <center>Replace Items</center>
                                                    </th>
                                                </tr>
                                                <tr class="replace-tr">
                                                    <th colspan="3" class="return-replace-item-th">
                                                        <table class="return-replace-item-container pos-orders-container">
                                                            <thead>
                                                                <th>Product</th>
                                                                <th>Price</th>
                                                                <th>Qty</th>
                                                                <th>Amt</th>
                                                                <th>Del</th>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
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
                                                                    <input type="number" readonly name="voucher" id="voucher" class="form-control text-primary" value="<?php echo $return['RETURN_AMOUNT'] ?>" required>
                                                                    <label for=vouchert">Voucher</label>
                                                                </div>

                                                                <div>
                                                                    <input type="submit" name="replace" id="replace" class="btn btn-primary replace" value="Replace" disabled>
                                                                </div>
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
                                                                    <input type="number" name="payment" id="payment" class="form-control text-primary" required disabled>
                                                                    <label for="payment">Payment</label>
                                                                    <span class="payment-required text-primary" id="payment-required-span">

                                                                    </span>
                                                                </div>

                                                                <div class="input">
                                                                    <input type="number" name="change" id="change" class="form-control text-success" readonly required min="0" value="0.00" oninput="validity.valid||(value='0');">
                                                                    <label for="Change">Change</label>
                                                                </div>
                                                                <?php
                                                                $tax_result = $conn->query("SELECT TAX_PERCENTAGE FROM tax WHERE TAX_ID = 1");
                                                                $tax = $tax_result->fetch_assoc();
                                                                $tax_percent = $tax['TAX_PERCENTAGE'];

                                                                ?>
                                                                <input type="hidden" name="tax" id="tax" value="<?php echo $tax_percent ?>">
                                                            </div>
                                                        </div>
                                                    </th>

                                                    <td colspan="2" class="pos-search-th">
                                                        <div class="search-container">
                                                            <form>
                                                                <input type="text" id="search-product" class="form-control" placeholder="Search Product...">
                                                            </form>
                                                        </div>
                                                        <div id="search-response-container" class="search-response-container">

                                                        </div>
                                                    </td>
                                                </tr>
                                    <?php
                                            }
                                        }
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="5">
                                            <center class="p-5 text-danger">No return transaction found.</center>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>


<?php
                    } else {
                        echo '
                        <head>
                        <link rel="stylesheet" href="../css/access-denied.css">
                        </head>
                        <div class="access-denied">
                              <h1>Access Denied</h1>
                              <h5>Invalid to access this page. 1</h5>
                          </div>';
                    }
                } else {
                    echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>The return process for this transaction has expired.</h5>
              </div>';
                }
            } else {
                echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>Invalid to access this page. 2</h5>
              </div>';
            }
        } else {
            echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>Invalid to access this page. 3</h5>
              </div>';
        }
    } else {
        echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>Invalid to access this page. 4</h5>
              </div>';
    }
} else {
    header("Location: ../index.php");
    exit;
}
