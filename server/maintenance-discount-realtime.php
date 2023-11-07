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
        $discount_sql = "SELECT * FROM discount";
        $discount_result = $conn->query($discount_sql);
        if ($discount_result->num_rows > 0) {
            while ($discount_row = $discount_result->fetch_assoc()) {
                $discount_status = $discount_row['DISCOUNT_STATUS'];
                if ($discount_status === 'active') {
?>

                    <tr>
                        <td><?php echo $discount_row['DISCOUNT_NAME'] ?></td>
                        <td class="td-discount-input"><span></span><input type="number" class="form-control discount-input" value="<?php echo $discount_row['DISCOUNT_PERCENTAGE'] ?>"></td>
                        <td>
                            <input type="submit" class="save-discount btn btn-primary" id="<?php echo $discount_row['DISCOUNT_ID'] ?>" value="Save">
                            <input type="submit" class="delete-discount btn btn-danger" id="<?php echo $discount_row['DISCOUNT_ID'] ?>" value="Disable">
                        </td>
                    </tr>
                <?php
                } else {
                ?>
                    <tr>
                        <td class="text-danger"><?php echo $discount_row['DISCOUNT_NAME'] ?></td>
                        <td class="td-discount-input"><span></span><input type="number" class="form-control discount-input" value="<?php echo $discount_row['DISCOUNT_PERCENTAGE'] ?>"></td>
                        <td>
                            <input type="submit" class="save-discount btn btn-primary" id="<?php echo $discount_row['DISCOUNT_ID'] ?>" value="Save">
                            <input type="submit" class="enable-discount btn btn-success" id="<?php echo $discount_row['DISCOUNT_ID'] ?>" value="Enable">
                        </td>
                    </tr>
            <?php
                }
            }
        } else {
            ?>
            <tr>
                <td colspan="3">
                    <center class="text-danger p-5">No Discount Found</center>
                </td>
            </tr>
<?php
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
?>