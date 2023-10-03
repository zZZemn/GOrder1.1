<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_GET['selectedTable'])) {
            $tableName = $_GET['selectedTable'];
            if ($tableName == 'delivery') {
                $getBackUpSql = "SELECT d.*, s.NAME FROM `delivery` d 
                                 JOIN supplier s ON d.SUPPLIER_ID = s.SUPPLIER_ID
                                 WHERE d.DELIVERY_STATUS = 'deleted'";
                $getBackUpResult = $conn->query($getBackUpSql);

?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Supplier</th>
                            <th>Delivery Date</th>
                            <th>Delivery Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($getBackUpResult->num_rows > 0) {
                            while ($getBackupRow = $getBackUpResult->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?= $getBackupRow['DELIVERY_ID'] ?></td>
                                    <td><?= $getBackupRow['NAME'] ?></td>
                                    <td><?= $getBackupRow['DELIVERY_DATE'] ?></td>
                                    <td><?= $getBackupRow['DELIVERY_PRICE'] ?></td>
                                    <td><button type="button" id="restore" data-table="delivery" data-id="<?= $getBackupRow['DELIVERY_ID'] ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5">
                                    <center>No Deliver Found</center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } elseif ($tableName == 'products') {
                $getBackUpSql = "SELECT * FROM `products` WHERE `PRODUCT_STATUS` = 'deleted'";
                $getBackUpResult = $conn->query($getBackUpSql);
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>MG</th>
                            <th>G</th>
                            <th>ML</th>
                            <th>Selling Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($getBackUpResult->num_rows > 0) {
                            while ($getBackupRow = $getBackUpResult->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?= $getBackupRow['PRODUCT_ID'] ?></td>
                                    <td><?= $getBackupRow['PRODUCT_NAME'] ?></td>
                                    <td><?= $getBackupRow['MG'] ?></td>
                                    <td><?= $getBackupRow['G'] ?></td>
                                    <td><?= $getBackupRow['ML'] ?></td>
                                    <td><?= $getBackupRow['SELLING_PRICE'] ?></td>
                                    <td><button type="button" id="restore" data-table="products" data-id="<?= $getBackupRow['PRODUCT_ID'] ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="8">
                                    <center>No Product Found</center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } elseif ($tableName == 'supplier') {
                $getBackUpSql = "SELECT * FROM `supplier` WHERE `SUPPLIER_STATUS` = 'deleted'";
                $getBackUpResult = $conn->query($getBackUpSql);
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Supplier ID</th>
                            <th>Supplier Name</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($getBackUpResult->num_rows > 0) {
                            while ($getBackupRow = $getBackUpResult->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?= $getBackupRow['SUPPLIER_ID'] ?></td>
                                    <td><?= $getBackupRow['NAME'] ?></td>
                                    <td><?= $getBackupRow['ADDRESS'] ?></td>
                                    <td><button type="button" id="restore" data-table="supplier" data-id="<?= $getBackupRow['SUPPLIER_ID'] ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="8">
                                    <center>No Supplier Found</center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
<?php
            } else {
            }
        } else {
            echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
