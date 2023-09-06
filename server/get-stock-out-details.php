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
            $id = $_GET['id'];

            $get_sql = "SELECT sod.*, i.EXP_DATE, p.PRODUCT_NAME
            FROM stock_out_details AS sod
            JOIN inventory AS i ON sod.INV_ID = i.INV_ID
            JOIN products AS p ON i.PRODUCT_ID = p.PRODUCT_ID
            WHERE sod.STOCK_OUT_ID = '$id'";

            if ($get_result = $conn->query($get_sql)) {
                if ($get_result->num_rows > 0) {
                    $row = 0;
                    while ($get = $get_result->fetch_assoc()) {
                        $row++;
?>
                        <tr>
                            <td><?= $row ?></td>
                            <td><?= $get['PRODUCT_NAME'] ?></td>
                            <td><?= $get['QTY'] ?></td>
                            <td><?= $get['EXP_DATE'] ?></td>
                            <td>
                                <button type="button" class="btn-delete btn btn-danger"
                                data-invid="<?= $get['ID'] ?>"
                                ><i class="fa-solid fa-trash"></i> Delete</button>
                            </td>
                        </tr>
<?php
                    }
                }
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
