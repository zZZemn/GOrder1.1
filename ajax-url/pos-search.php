<?php
include('../database/db.php');

if (isset($_POST['query'])) {
    $query = filter_var($_POST['query'], FILTER_SANITIZE_STRING);

    $search_sql = "SELECT * FROM products WHERE (PRODUCT_NAME LIKE '%$query%' OR PRODUCT_ID LIKE '%$query%' OR PRODUCT_CODE LIKE '%$query%') AND PRODUCT_STATUS = 'active' LIMIT 15";
    $search_result = $conn->query($search_sql);
    if ($search_result->num_rows > 0) {
        while ($search = $search_result->fetch_assoc()) {
            $isVatable = ($search['VATABLE'] == 1) ? 1 : 0;
            $isPrescribe = ($search['PRESCRIBE'] == 1) ? 1 : 0;
            $isDiscountable = ($search['DISCOUNTABLE'] == 1) ? 1 : 0;
            $pro_qty = 0;

            $pro_id = $search['PRODUCT_ID'];
            $quantityCheck_sql = "SELECT * FROM inventory WHERE PRODUCT_ID = $pro_id";
            $quantityCheck_result = $conn->query($quantityCheck_sql);
            if ($quantityCheck_result->num_rows > 0) {
                while ($quantityCheck_result_row = $quantityCheck_result->fetch_assoc()) {
                    $pro_qty += $quantityCheck_result_row['QUANTITY'];
                }
            }

            $order_sql = "SELECT od.*
                          FROM `order_details` od
                          JOIN `order` o ON od.TRANSACTION_ID = o.TRANSACTION_ID
                          WHERE od.PRODUCT_ID = '$pro_id' AND (o.STATUS = 'Waiting' OR o.STATUS = 'Accepted');
                          ";
            $order_result = $conn->query($order_sql);
            if ($order_result->num_rows > 0) {
                while ($order_row = $order_result->fetch_assoc()) {
                    $pro_qty -= $order_row['QTY'];
                }
            }

            $productName = $search['PRODUCT_NAME'];
            (!empty($search['G'])) ? $productName .= "  <sup>" . $search['G'] . "g</sup>" : '';
            (!empty($search['MG'])) ? $productName .= "  <sup>" . $search['MG'] . "mg</sup>" : '';
            (!empty($search['ML'])) ? $productName .= "  <sup>" . $search['ML'] . "ml</sup>" : '';

            $result = "<form class='product-select' method='post'>
            <input type='hidden' name='productCode' value='" . $search['PRODUCT_CODE'] . "'>
            <input type='hidden' name='isPrescribe' value='" . $isPrescribe . "'>
            <input type='hidden' name='isDiscountable' value='" . $isDiscountable . "'>
            <input type='hidden' name='isVatable' value='" . $isVatable . "'>
            <input type='hidden' name='quantity_left' value='" . $pro_qty . "'>
            <input type='hidden' name='product_id' value='" . $search['PRODUCT_ID'] . "'>
            <input type='hidden' name='product_name' value='" . $productName . "'>
            <input type='hidden' name='selling_price' value='" . $search['SELLING_PRICE'] . "'>
            <input type='hidden' name='g' value='" . $search['G'] . "'>
            <input type='hidden' name='mg' value='" . $search['MG'] . "'>
            <input type='hidden' name='ml' value='" . $search['ML'] . "'>
            <button type='submit' name='submit_pro_id'>
                <div>" . $search['PRODUCT_NAME'];

            if (!empty($search['G'])) {
                $result .= " | <sup>" . $search['G'] . "g</sup>";
            }
            if (!empty($search['MG'])) {
                $result .= " | <sup>" . $search['MG'] . "mg</sup>";
            }
            if (!empty($search['ML'])) {
                $result .= " | <sup>" . $search['ML'] . "ml</sup>";
            }

            $result .= "</div>
            <div class='details-container'>
                <div class='detail'>" . $search['SELLING_PRICE'] . " - " . $pro_qty . "pc/s</div>
            </div>
        </button>
        </form>";

            echo $result;
        }
    } else {
        $result = "<center class='mt-5 text-danger'>No Product Found</center>";
        echo $result;
    }
}
