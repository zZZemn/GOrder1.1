<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        // customers
        $sales_details_sql = "SELECT
                              COUNT(*) as numberOfSales,
                              sd.PRODUCT_ID,
                              p.PRODUCT_NAME,
                              SUM(sd.QUANTITY) AS total_qty_sold
                              FROM sales s INNER JOIN sales_details sd ON s.transaction_id = sd.transaction_id
                              INNER JOIN products p ON sd.product_id = p.product_id
                              WHERE s.DATE = CURDATE()
                              GROUP BY sd.product_id, p.product_name
                              ORDER BY total_qty_sold DESC LIMIT 1;";
        if ($sales_details = $conn->query($sales_details_sql)) {
            if ($sales_details->num_rows > 0) {
                $product_sale = $sales_details->fetch_assoc();
                $freqBItem = $product_sale['PRODUCT_NAME'];
                $totSales = $product_sale['numberOfSales'];
            } else {
                $freqBItem = 'NA';
                $totSales = 0;
            }
        } else {
            $freqBItem = 'NA';
            $totSales = 0;
        }

        // products
        $return_details_sql = "SELECT
        COUNT(*) AS total_returns,
        (
            SELECT RETURN_REASON
            FROM `return`
            WHERE RETURN_DATE = CURDATE()
            GROUP BY RETURN_REASON
            ORDER BY COUNT(*) DESC
            LIMIT 1
        ) AS most_common_return_reason
        FROM
        `return`
        WHERE
        RETURN_DATE = CURDATE();";

        if ($return_details = $conn->query($return_details_sql)) {
            if ($return_details->num_rows > 0) {
                $product_return = $return_details->fetch_assoc();
                $most_return_reason = $product_return['most_common_return_reason'];
                $totreturn = $product_return['total_returns'];
            } else {
                $most_return_reason = 'NA';
                $totreturn = 0;
            }
        } else {
            $most_return_reason = 'NA';
            $totreturn = 0;
        }

        $data = [
            "customer" => [
                '0' => $freqBItem,
                '1' => $totSales
            ],
            "product" => [
                '0' => $most_return_reason,
                '1' => $totreturn
            ]
        ];

        echo json_encode($data);
?>
        
<?php
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
?>