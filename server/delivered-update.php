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
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            $del_query = "SELECT * FROM inventory WHERE DELIVERY_ID = '$id' ORDER BY EXP_DATE";
            $del_query_result = $conn->query($del_query);
            if ($del_query_result->num_rows > 0) {
                while ($row = $del_query_result->fetch_assoc()) {
                    $pro_id = $row['PRODUCT_ID'];
                    $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = $pro_id";
                    $product_result = $conn->query($product_sql);
                    $product = $product_result->fetch_assoc();
?>
                    <tr>
                        <td><?php echo $product['PRODUCT_NAME'] ?></td>
                        <td><?php echo $row['SUPPLIER_PRICE'] ?></td>
                        <td><?php echo $product['SELLING_PRICE'] ?></td>
                        <td class="
                        <?php
                        $row_mark_up = $row['MARK_UP'];
                        echo ($row_mark_up < 1) ? 'lessThanZeroMarkUp' : '';
                        ?>">
                            <?php
                            echo $row_mark_up;
                            ?>
                        </td>
                        <td>
                            <?php 
                                $exp_date = $row['EXP_DATE'];
                                echo ($exp_date === '0000-00-00') ? 'No expiration date' : $exp_date;
                            ?>
                        </td>
                        <td><?php echo $row['DEL_QUANTITY'] ?></td>
                        <td><?php echo $row['QUANTITY'] ?></td>
                        <td class="actions"> 
                            <a href="#" class="btn btn-primary text-light" id="edit-delivered" data-inv_id="<?php echo $row['INV_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a href="#" class="btn btn-dark text-light"><i class="fa-solid fa-trash"></i></a>
                        </td>
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
