<?php
function backToReturnRequests()
{
    header('Location: gorder-return-requests.php');
    exit;
}

include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();


    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $check_id_sql = $conn->query("SELECT * FROM `return` WHERE `RETURN_ID` = '$id'");
            if ($check_id_sql->num_rows > 0) {
                $return_det = $check_id_sql->fetch_assoc();
                $transaction_id = $return_det['TRANSACTION_ID'];
                $sales_sql = "SELECT s.DATE, s.TIME, c.*, bgy.*, muni.*, prov.*, reg.*
                FROM `sales` AS s
                JOIN `customer_user` AS c ON s.CUST_ID = c.CUST_ID
                JOIN `barangay` AS bgy ON c.BARANGAY_ID = bgy.BARANGAY_ID
                JOIN `municipality` AS muni ON bgy.MUNICIPALITY_ID = muni.MUNICIPALITY_ID
                JOIN `province` AS prov ON muni.PROVINCE_ID = prov.PROVINCE_ID
                JOIN `region` AS reg ON prov.REGION_ID = reg.REGION_ID
                WHERE s.TRANSACTION_ID = '$transaction_id';";
                $sales_result = $conn->query($sales_sql);
                if ($sales_result->num_rows > 0) {
                    $sales = $sales_result->fetch_assoc();
                } else {
                    backToReturnRequests();
                }
?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon" />
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
                    </style>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                    <link rel="stylesheet" href="../css/ret-req-details.css">
                    <title>Return Request | <?= $id ?></title>
                </head>

                <body>
                    <center>
                        <h3>
                            Return Request
                        </h3>
                    </center>
                    <div class="container top-container">
                        <div class="top-container-left">
                            <div class="input-container">
                                <input type="text" class="form-control" id="transaction-id" readonly value="<?= $transaction_id ?>">
                                <label for="transaction-id">Transaction ID</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="cust-name" readonly value="<?= ucwords($sales['FIRST_NAME'] . ' ' . $sales['MIDDLE_INITIAL'] . ' ' . $sales['LAST_NAME']) ?>">
                                <label for="cust-name">Customer</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="sales-date" readonly value="<?= $sales['DATE'] ?>">
                                <label for="sales-date">Date</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="sales-time" readonly value="<?= $sales['TIME'] ?>">
                                <label for="sales-time">Time</label>
                            </div>
                        </div>
                        <div class="top-container-right">
                            <div class="input-container">
                                <input type="text" class="form-control" id="req-id" readonly value="<?= $return_det['RETURN_ID'] ?>">
                                <label for="req-id">Request ID</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="req-date" readonly value="<?= $return_det['RETURN_DATE'] ?>">
                                <label for="req-date">Request Date</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="req-amount" readonly value="<?= $return_det['RETURN_AMOUNT'] ?>">
                                <label for="req-amount">Request Amount</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="req-reason" readonly value="<?= $return_det['RETURN_REASON'] ?>">
                                <label for="req-reason">Request Reason</label>
                            </div>
                        </div>
                    </div>
                    <div class="container address-conainer">
                        <div class="input-container">
                            <input type="text" readonly class="form-control" id="address" value="<?= $sales['UNIT_STREET'] . ', ' . $sales['BARANGAY'] . ', ' . $sales['MUNICIPALITY'] . ', ' . $sales['PROVINCE'] . ', ' . $sales['REGION'] ?>">
                            <label for="address">Address</label>
                        </div>
                    </div>
                    <div class="container table-container">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Inventory ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $product_sql = "SELECT ri.*, i.*, p.* FROM
                                                `return_items` AS ri
                                                JOIN `inventory` AS i ON ri.INV_ID = i.INV_ID
                                                JOIN `products` AS p ON i.PRODUCT_ID = p.PRODUCT_ID
                                                WHERE ri.RETURN_ID = '$id'";
                                if ($product_result = $conn->query($product_sql)) {
                                    if ($product_result->num_rows > 0) {
                                        $row = 0;
                                        while ($product = $product_result->fetch_assoc()) {
                                            $row++;
                                ?>
                                            <tr>
                                                <td><?= $row ?></td>
                                                <td><?= $product['INV_ID'] ?></td>
                                                <td><?= $product['PRODUCT_NAME'] ?></td>
                                                <td><?= $product['QTY'] ?></td>
                                                <td><?= number_format(floatval($product['QTY'] * $product['SELLING_PRICE']), 2) ?></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="5">
                                        <center class="text-success p-3">Select rider to accept this return.</center>
                                        <select class="form-control" id="select-rider">
                                            <option disabled selected>Select Rider</option>
                                            <option value=""></option>
                                            <?php
                                            $rider_sql = $conn->query("SELECT * FROM `employee` WHERE `EMP_TYPE` = 'Rider'");
                                            if ($rider_sql->num_rows > 0) {
                                                while ($rider = $rider_sql->fetch_assoc()) {
                                            ?>
                                                    <option value="<?= $rider['EMP_ID'] ?>" <?php echo ($rider['EMP_ID'] === $return_det['RIDER_ID']) ? 'selected' : '' ?> ><?= $rider['FIRST_NAME'] . ' ' . $rider['MIDDLE_INITIAL'] . ' ' . $rider['LAST_NAME'] ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
                    <script src="../js/return-request-details.js"></script>
                </body>

                </html>

<?php
            } else {
                backToReturnRequests();
            }
        } else {
            backToReturnRequests();
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
