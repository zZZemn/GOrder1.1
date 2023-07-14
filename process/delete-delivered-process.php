<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp['EMP_TYPE'] === 'Admin' && isset($_GET['inv_id'])) {
        $inv_id = $_GET['inv_id'];
        if (is_numeric($inv_id)) {
            include('../time-date.php');
            $editDate = $currentDate;
            $editTime = $currentTime;
            $emp_id = isset($emp['EMP_ID']) ? intval($emp['EMP_ID']) : null;

            $inv_sql = "SELECT SUPPLIER_PRICE, DEL_QUANTITY, DELIVERY_ID FROM inventory WHERE INV_ID = '$inv_id'";

            if ($inv_result = $conn->query($inv_sql)) {
                if ($inv_result->num_rows > 0) {
                    $inv = $inv_result->fetch_assoc();
                    $del_id = $inv['DELIVERY_ID'];
                    $supplier_price = $inv['SUPPLIER_PRICE'];
                    $del_qty = $inv['DEL_QUANTITY'];
                    $total_item_price = $supplier_price * $del_qty;

                    $update_delivery = "UPDATE `delivery` SET `DELIVERY_PRICE` = DELIVERY_PRICE - $total_item_price WHERE DELIVERY_ID = $del_id";

                    $deleteInventory = "DELETE FROM `inventory` WHERE INV_ID = '$inv_id'";

                    $del_del_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                            ('$emp_id','Delete INV-$inv_id in inventory','$editDate','$editTime')";

                    if ($conn->query($deleteInventory) === TRUE && $conn->query($del_del_log) === TRUE && $conn->query($update_delivery) === TRUE) {
                        echo 'ok';
                    } else {
                        echo 'not';
                    }
                } else {
                    echo 'not';
                }
            } else {
                echo 'not';
            }
        } else {
            echo 'not';
        }
    } else {
        echo 'not';
    }
} else {
    header("Location: ../index.php");
    exit;
}
