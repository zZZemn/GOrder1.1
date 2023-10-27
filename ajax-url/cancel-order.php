<?php
if (isset($_POST['order_id'], $_POST['reason'])) {
    include('../database/db.php');
    $orderId = $_POST['order_id'];
    $reason = $_POST['reason'];

    $returnProductToInventorySql = "SELECT s.*, sd.* FROM sales_details sd JOIN sales s ON sd.TRANSACTION_ID = s.TRANSACTION_ID WHERE s.ORDER_ID = '$orderId'";
    $returnProductToInventoryResult = $conn->query($returnProductToInventorySql);

    if ($returnProductToInventoryResult) {
        $updates = array();

        while ($returnProductToInvRow = $returnProductToInventoryResult->fetch_assoc()) {
            $invId = $returnProductToInvRow['INV_ID'];
            $qty = $returnProductToInvRow['QUANTITY'];

            // Create an update statement and add it to the updates array
            $updates[] = "UPDATE `inventory` SET `QUANTITY` = `QUANTITY` + $qty WHERE `INV_ID` = $invId";
        }

        // Batch update the inventory
        $batchUpdateSql = implode(';', $updates);
        if ($conn->multi_query($batchUpdateSql)) {
            while ($conn->more_results() && $conn->next_result()) {
                $result = $conn->use_result();
                if ($result instanceof mysqli_result) {
                    $result->free();
                }
            }

            $deleteSalesSql = "DELETE FROM `sales` WHERE `ORDER_ID` = '$orderId'";
            $deleteSalesDetailsSql = "DELETE FROM `sales_details` WHERE `TRANSACTION_ID` IN (SELECT `TRANSACTION_ID` FROM `sales` WHERE `ORDER_ID` = '$orderId')";
            $sql = "UPDATE `order` SET `STATUS`='Rejected', `MESSAGE`='$reason' WHERE `TRANSACTION_ID` = '$orderId'";

            if ($conn->query($deleteSalesDetailsSql)) {
                if ($conn->query($deleteSalesSql) && $conn->query($sql)) {
                    echo '200';
                } else {
                    echo '400';
                }
            } else {
                echo 'Error deleting data in sales and sales_details tables: ' . $conn->error;
            }
        } else {
            echo 'Batch update failed: ' . $conn->error;
        }
    } else {
        echo 'Error fetching data: ' . $conn->error;
    }
}
