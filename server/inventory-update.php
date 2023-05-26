<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();


    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {


?>
        <table class="inventory-container table table-striped">
            <thead>
                <tr>
                    <th>Inventory ID</th>
                    <th>Product</th>
                    <th>Expiration Date</th>
                    <th>Qty</th>
                    <th>View</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $inventory_sql = "SELECT * FROM inventory WHERE QUANTITY > 0 ORDER BY EXP_DATE";
                $inventory_result = $conn->query($inventory_sql);
                if ($inventory_result->num_rows > 0) {
                    while ($row = $inventory_result->fetch_assoc()) {
                        $pro_id = $row['PRODUCT_ID'];
                        $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = $pro_id";
                        $product_result = $conn->query($product_sql);
                        $product = $product_result->fetch_assoc();
                ?>
                        <tr>

                            <td><?php echo $row['INV_ID'] ?></td>
                            <td><?php echo $product['PRODUCT_NAME'] ?></td>
                            <td><?php echo $row['EXP_DATE'] ?></td>
                            <td><?php echo $row['QUANTITY'] ?></td>
                            <td>
                                <a href="delivered-products.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-solid fa-eye"></i></a>
                            </td>

                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center text-danger p-5 m-5" style="font-size: 15px;">Empty</td>
                    </tr>
                <?php
                }
                ?>
                <tr>

                </tr>
            </tbody>
        </table>
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