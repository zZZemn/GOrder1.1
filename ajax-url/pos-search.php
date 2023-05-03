<?php
include('../database/db.php');

if (isset($_POST['query'])) {
    // get the search query from the POST data
    $query = $_POST['query'];

    // connect to your database here

    // prepare a SQL statement to search for products
    $search_sql = "SELECT * FROM products WHERE (PRODUCT_NAME LIKE '%$query%' OR PRODUCT_ID LIKE '%$query%' OR PRODUCT_CODE LIKE '%$query%') AND PRODUCT_STATUS = 'active' LIMIT 15";
    $search_result = $conn->query($search_sql);
    if ($search_result->num_rows > 0) {
        while ($search = $search_result->fetch_assoc()) {
            $pro_qty = 0;

            $pro_id = $search['PRODUCT_ID'];
            $quantityCheck_sql = "SELECT * FROM inventory WHERE PRODUCT_ID = $pro_id";
            $quantityCheck_result = $conn->query($quantityCheck_sql);
            if ($quantityCheck_result->num_rows > 0) {
                while ($quantityCheck_result_row = $quantityCheck_result->fetch_assoc()) {
                    $pro_qty += $quantityCheck_result_row['QUANTITY'];
                }
            }

            $result = "<form class='product-select' method='post'>
                        <input type='hidden' name='product_id' value='" . $search['PRODUCT_ID'] . "'>
                        <input type='hidden' name='product_name' value='" . $search['PRODUCT_NAME'] . "'>
                        <input type='hidden' name='selling_price' value='" . $search['SELLING_PRICE'] . "'>
                        <input type='hidden' name='unit_meas' value='" . $search['UNIT_MEASUREMENT'] . "'>
                        <button type='submit' name='submit_pro_id'>" . $search['PRODUCT_NAME'] . " " . $search['UNIT_MEASUREMENT'] . "
                            <div class='details-container'>
                                <div class='detail'>" . $search['SELLING_PRICE'] . " - " . $pro_qty . "pc/s</div>
                            </div>
                        </button>
                    </form>";

            echo $result;
        }
    }
    else {
        echo "";
    }
}
