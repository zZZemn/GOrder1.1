<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $transactionID = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $s_details_sql = "SELECT * FROM sales_details WHERE TRANSACTION_ID = $transactionID";
        $s_details_result = $conn->query($s_details_sql);
        if ($s_details_result->num_rows > 0) {
            $sales_sql = "SELECT * FROM sales WHERE TRANSACTION_ID = $transactionID";
            $sales_result = $conn->query($sales_sql);
            if (!$sales_result) {
                echo "Query Error: " . $conn->error . "<br>";
            }            
            if ($sales_result->num_rows > 0) {
                $sales = $sales_result->fetch_assoc();

                $empID = $sales['EMP_ID'];
                $process_by_sql = "SELECT * FROM employee WHERE EMP_ID = $empID";
                $process_by_result = $conn->query($process_by_sql);
                if ($process_by_result->num_rows > 0) {
                    $employee = $process_by_result->fetch_assoc();
                    $emp_name = $employee['FIRST_NAME'] . " " . $employee['LAST_NAME'];
                } else {
                    echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>Invalid to access this page. 1</h5>
              </div>';

                    exit();
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

                exit();
            }
?>

            <head>
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
                </style>
                <link rel="stylesheet" href="../css/view-invoice.css">
                <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
                <title>GOrder | Invoice</title>
            </head>
            <table class="invoice-container">
                <tr class="heading">
                    <td colspan="4"><center>Golden Gate Drugstore</center></td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><center>Patubig, Marilao, Bulacan</center></td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><center><?php echo $sales['DATE'] . " | " . $sales['TIME'] ?></center></td>
                </tr>
                <tr class="headers">
                    <td>Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Amount</td>
                </tr>
                <?php
                while ($row = $s_details_result->fetch_assoc()) {
                    $productID = $row['PRODUCT_ID'];
                    $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = $productID";
                    $product_result = $conn->query($product_sql);
                    if ($product_result->num_rows > 0) {
                        $product = $product_result->fetch_assoc();
                    } else {
                        echo '
                            <head>
                            <link rel="stylesheet" href="../css/access-denied.css">
                            </head>
                            <div class="access-denied">
                                <h1>Access Denied</h1>
                                <h5>Invalid to access this page. 3</h5>
                            </div>';
                        exit();
                    }
                ?>
                    <tr>
                        <td><?php echo $product['PRODUCT_NAME'] ?></td>
                        <td><?php echo $product['SELLING_PRICE'] ?></td>
                        <td class="qty"><?php echo $row['QUANTITY'] ?></td>
                        <td><?php echo $row['AMOUNT'] ?></td>
                    </tr>
                <?php
                }
                ?>
                <tr class="line">
                    <td colspan="4"><hr></td>
                </tr>
                <tr>
                    <td>Subtotal</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['SUBTOTAL'] ?></td>
                </tr>
                <tr>
                    <td>VAT</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['VAT'] ?></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['DISCOUNT'] ?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['TOTAL'] ?></td>
                </tr>
                <tr>
                    <td>Payment</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['PAYMENT'] ?></td>
                </tr>
                <tr>
                    <td>Change</td>
                    <td>:</td>
                    <td colspan="2" class="computed"><?php echo $sales['CHANGE'] ?></td>
                </tr>

                <tr class="process-by">
                    <td colspan="4">Process By: <?php echo $emp_name ?> >>>>> Transaction ID: <?php echo $transactionID ?></td>
                </tr>
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
                  <h5>Invalid to access this page. 5</h5>
              </div>';
    }
} else {
    header("Location: ../index.php");
    exit();
}
