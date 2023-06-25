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

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        $sales_sql = "SELECT * FROM sales WHERE DATE = '$currentDate' AND PAYMENT >= TOTAL ORDER BY DATE, TIME";
        $sales_result = $conn->query($sales_sql);
        if ($sales_result->num_rows > 0) {
?>
                <?php
                while ($sales = $sales_result->fetch_assoc()) {
                    $process_empID = $sales['EMP_ID'];
                    $emp_result = $conn->query("SELECT * FROM employee WHERE EMP_ID = '$process_empID'");
                    if ($emp_result->num_rows > 0) {
                        $emp = $emp_result->fetch_assoc();
                        $emp_name = $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'];
                    } else {
                        $emp_name = '';
                    }
                ?>
                    <tr>
                        <td><?php echo $sales['TRANSACTION_ID'] ?></td>
                        <td><?php echo $sales['TRANSACTION_TYPE'] ?></td>
                        <td><?php echo $sales['CUST_TYPE'] ?></td>
                        <td><?php echo $sales['TIME'] ?></td>
                        <td><?php echo $sales['SUBTOTAL'] ?></td>
                        <td><?php echo $sales['VAT'] ?></td>
                        <td><?php echo $sales['DISCOUNT'] ?></td>
                        <td><?php echo $sales['TOTAL'] ?></td>
                        <td><?php echo $sales['PAYMENT'] ?></td>
                        <td><?php echo $sales['CHANGE'] ?></td>
                        <td><?php echo $sales['UPDATED_TOTAL'] ?></td>
                        <td><?php echo $emp_name ?></td>
                        <td class="action-td"><a href="view-invoice.php?id=<?php echo $sales['TRANSACTION_ID'] ?>" target="_blank" class="btn btn-primary"><i class='fa-solid fa-eye'></i></a></td>
                    </tr>
                <?php
                }
                ?>
        <?php
        } else {
        ?>
            <tr>
                <td colspan="13">
                    <center class="text-danger">No sales found</center>
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
