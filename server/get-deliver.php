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
        if (isset($_GET['search']) && isset($_GET['supplier']) && isset($_GET['priceSort'])) {
            $priceSort = $_GET['priceSort'];
            $search = $_GET['search'];
            if (!empty($search) && $search != 'undefined') {
                $del_sql = "SELECT * FROM delivery WHERE (DELIVERY_ID LIKE '%$search%' OR DELIVERY_DATE LIKE '%$search%' OR DELIVERY_PRICE LIKE '%$search%') AND DELIVERY_STATUS = 'active'";
            } elseif ($_GET['supplier'] !== 'all') {
                $supplier = $_GET['supplier'];
                if ($priceSort === 'asc') {
                    $del_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = '$supplier' AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE ASC";
                } elseif ($priceSort === 'desc') {
                    $del_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = '$supplier' AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE DESC";
                } else {
                    $del_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = '$supplier' AND DELIVERY_STATUS = 'active'";
                }
            } else {
                if ($priceSort === 'asc') {
                    $del_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE ASC";
                } elseif ($priceSort === 'desc') {
                    $del_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE DESC";
                } else {
                    $del_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active'";
                }
            }
            if ($del_result = $conn->query($del_sql)) {
                if ($del_result->num_rows > 0) {
                    while ($del_row = $del_result->fetch_assoc()) {
                        $sup_id = $del_row['SUPPLIER_ID'];
                        $sup_sql = "SELECT `NAME` FROM supplier WHERE SUPPLIER_ID = '$sup_id'";
                        $sup_result = $conn->query($sup_sql);
                        if ($sup_result->num_rows > 0) {
                            $sup = $sup_result->fetch_assoc();
                            $supname = $sup['NAME'];
                        } 
                        // else {
                        //     $supname = '';
                        // }
?>
                        <tr>
                            <td><?php echo $del_row['DELIVERY_ID'] ?></td>
                            <td><?php echo $supname ?></td>
                            <td><?php echo $del_row['DELIVERY_DATE'] ?></td>
                            <td><?php echo $del_row['DELIVERY_PRICE'] ?></td>
                            <td>
                                <a href="delivered-products.php?del_id=<?php echo $del_row['DELIVERY_ID'] ?>"><i class="fa-regular fa-eye"></i></a>
                                <a href="#" class="edit-deliver-link" id="edit-deliver-link" data-del_id="<?php echo $del_row['DELIVERY_ID'] ?>" data-supp_lier="<?php echo $del_row['SUPPLIER_ID'] ?>" data-del_date="<?php echo $del_row['DELIVERY_DATE'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                <a href="#" id="delete-deliver" data-del_id="<?php echo $del_row['DELIVERY_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="5">
                            <center>No Delivery Found</center>
                        </td>
                    </tr>
<?php
                }
            }
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
