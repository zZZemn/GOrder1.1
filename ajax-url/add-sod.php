<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['id']) && isset($_POST['quantity']) && isset($_POST['soid'])) {
        $soid = $_POST['soid'];
        $id = $_POST['id'];
        $qty = $_POST['quantity'];

        $inventory_sql = "SELECT * FROM `inventory`
                          WHERE `PRODUCT_ID` = '$id'
                          AND `QUANTITY` > 0
                          ORDER BY `EXP_DATE` ASC, `PRODUCT_ID` ASC, `INV_ID` ASC";
        $inventory_result = $conn->query($inventory_sql);
        if ($inventory_result->num_rows > 0) {
            while ($inv = $inventory_result->fetch_assoc()) {
                $available_quantity = $inv['QUANTITY'];
                $subtracted_quantity = min($qty, $available_quantity);

                $inventory_sql = "UPDATE `inventory` 
                          SET `QUANTITY` = `QUANTITY` - $subtracted_quantity
                          WHERE `PRODUCT_ID` = '" . $id .
                    "' AND `EXP_DATE` = '" . $inv['EXP_DATE'] .
                    "' AND `INV_ID` = " . $inv['INV_ID'];
                $conn->query($inventory_sql);
                if ($subtracted_quantity > 0) {
                    $sql = "INSERT INTO `stock_out_details`(`STOCK_OUT_ID`, `INV_ID`, `QTY`) 
                                            VALUES ('$soid','" . $inv['INV_ID'] . "','$subtracted_quantity')";
                    $conn->query($sql);
                    $qty -= $subtracted_quantity;
                }

                if ($qty <= 0) {
                    break;
                }
            }
        } else {
            echo $id;
        }
    } else {
        echo 'not';
    }
} else {
    header("Location: ../index.php");
    exit;
}
