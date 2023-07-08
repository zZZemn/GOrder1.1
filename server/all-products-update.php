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
        if (isset($_GET['search']) && isset($_GET['cat']) && isset($_GET['sub_cat'])) {
            $searchItem = $_GET['search'];
            if (!empty($searchItem)) {
                $product_sql = "SELECT * FROM products WHERE (PRODUCT_NAME LIKE '%$searchItem%' OR PRODUCT_CODE LIKE '%$searchItem%' OR PRODUCT_ID LIKE '%$searchItem%') AND PRODUCT_STATUS = 'active'";
            } elseif ($_GET['sub_cat'] !== 'all') {
                $sub_cat = $_GET['sub_cat'];
                $product_sql = "SELECT * FROM products WHERE SUB_CAT_ID = '$sub_cat' AND PRODUCT_STATUS = 'active'";
            } elseif ($_GET['cat'] !== 'all') {
                $cat = $_GET['cat'];
                $product_sql = "SELECT p.*
                FROM products p
                INNER JOIN sub_category sc ON p.SUB_CAT_ID = sc.SUB_CAT_ID
                WHERE sc.CAT_ID = $cat AND p.PRODUCT_STATUS = 'active';";
            } else {
                $product_sql = "SELECT * FROM products WHERE PRODUCT_STATUS = 'active'";
            }

            $product_result = $conn->query($product_sql);
            if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
?>
                    <tr>
                        <td class="pro-code"><?php echo $product['PRODUCT_CODE'] ?></td>
                        <td><?php echo $product['PRODUCT_NAME'] ?></td>
                        <td class="unit-meas"><?php echo $product['UNIT_MEASUREMENT'] ?></td>
                        <td class="selling-price"><?php echo $product['SELLING_PRICE'] ?></td>
                        <td><?php echo '' ?></td>
                        <td><?php echo $product['CRITICAL_LEVEL'] ?></td>
                        <td class="actions">
                            <a class="description-hover"><i class="fa-solid fa-comment-medical"></i>
                                <span>
                                    <?php
                                    $row_description = "";
                                    ($product['DESCRIPTION'] === '') ? $row_description = "No Description" : $row_description = $row['DESCRIPTION'];
                                    echo $row_description;
                                    ?>
                                </span>
                            </a>
                            <a href="products-allproducts-edit.php?product_id=<?php echo $product['PRODUCT_ID'] ?>" class="make-me-dark"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a href="#" class="make-me-dark delete-product" id="delete-product" data-product_id="<?php echo $product['PRODUCT_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr class="no-pro-found">
                    <td colspan="7">No products Found</td>
                </tr>
<?php
            }
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
