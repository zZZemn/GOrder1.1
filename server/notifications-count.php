<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_status === 'active') {

        $product_sql = "SELECT PRODUCT_ID, PRODUCT_NAME, CRITICAL_LEVEL FROM products";
        $product_result = $conn->query($product_sql);
        $notification = 0;
        if ($product_result->num_rows > 0) {
            while ($product = $product_result->fetch_assoc()) {
                $product_id = $product['PRODUCT_ID'];
                $product_name = $product['PRODUCT_NAME'];
                $critical_level = $product['CRITICAL_LEVEL'];

                //expiration
                $inventory_expiration_sql = "SELECT INV_ID FROM inventory WHERE EXP_DATE <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) AND PRODUCT_ID = '$product_id'";
                $inventory_expiration_result = $conn->query($inventory_expiration_sql);
                if ($inventory_expiration_result->num_rows > 0) {
                    while ($inventory_expiration_row = $inventory_expiration_result->fetch_assoc()) {
                        $notification++;
                    }
                }

                //stocks
                $inventory_sql = "SELECT SUM(QUANTITY) AS total_qty FROM inventory WHERE PRODUCT_ID = '$product_id'";
                $inventory_result = $conn->query($inventory_sql);
                if ($inventory_result->num_rows > 0) {
                    $inventory = $inventory_result->fetch_assoc();
                    $product_qty = $inventory ? $inventory['total_qty'] : 0;
                    if ($product_qty == 0) {
                        $notification++;
                    } elseif ($product_qty <= $critical_level) {
                        $notification++;
                    }
                }
            }
        }
?>
        <span class="badge rounded-pill badge-notification bg-danger">
            <?php echo $notification ?>
        </span>
<?php
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
