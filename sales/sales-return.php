<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $transaction_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $sales_sql = "SELECT * FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
            $sales_result = $conn->query($sales_sql);
            if ($sales_result->num_rows > 0) {
                $sales = $sales_result->fetch_assoc();
                $sales_date = $sales['DATE'];
                $cust_id = $sales['CUST_ID'];
                $emp_id = $sales['EMP_ID'];

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

                        <head>
                            <meta charset="UTF-8">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <style>
                                @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
                            </style>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                            <link rel="stylesheet" href="../css/sales-return.css">
                            <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
                            <title>GOrder | Return</title>
                        </head>

                        <body>
                            <table class="table table-striped">
                                <thead>
                                    <tr class="return-products-tr">
                                        <th colspan="5" class="bg-dark text-light">
                                            RETURN PRODUCT
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
                                        $price_pc =  $amount / $qty; 
                                    ?>
                                    <input type="hidden" id="transaction_id" value="<?php echo $sales['TRANSACTION_ID'] ?>">
                                        <tr>
                                            <td><?php echo $product_name ?></td>
                                            <td class="text-center"><?php echo $salesD_row['QUANTITY'] ?></td>
                                            <td class="pl"><?php echo $salesD_row['AMOUNT'] ?></td>
                                            <td class="text-center"><?php echo $exp_date ?></td>
                                            <td class="text-center qty-td">
                                                <div class="qty-container-div">
                                                    <input type="hidden" id="price" value="<?php echo $price_pc ?>">
                                                    <input type="number" class="form-control" name="quantity" id="<?php echo $salesD_row['INV_ID'] ?>" placeholder="Enter Quantity" min="0" max="<?php echo htmlspecialchars($salesD_row['QUANTITY']) ?>" oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;">
                                                    <label class="alert-when-reach-maxlevel text-danger">Maximum level.</label>
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
                                        <td class="btn-add-return-td"><a href="#" id="submit_return" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Return</a></td>
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
                                    <tr class="return-details-center-tr">
                                        <th colspan="5" class="bg-dark text-light">
                                            <center>Return Details</center>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>

                            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
                            <script src="../js/sales-return.js"></script>
                        </body>
<?php
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
                  <h5>Invalid to access this page. 4</h5>
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
