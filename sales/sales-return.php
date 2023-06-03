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
                if ($sales_date >= $sevenDaysAgo) {
                    $sales_details_sql = "SELECT * FROM sales_details WHERE TRANSACTION_ID = '$transaction_id'";
                    $sales_details_result = $conn->query($sales_details_sql);
                    if ($sales_details_result->num_rows > 0) {
?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Expiration Date</th>
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
                                ?>

                                    <tr>
                                        <td><?php echo $product_name ?></td>
                                        <td><?php echo $salesD_row['QUANTITY'] ?></td>
                                        <td><?php echo $salesD_row['AMOUNT'] ?></td>
                                        <td><?php echo $exp_date ?></td>
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
