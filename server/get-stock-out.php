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
        if (isset($_GET['branch'])) {
            $branch = $_GET['branch'];
            $emp_id = $_GET['emp'];
            $search = $_GET['search'];

            if ($search !== '') {
                $response_sql = "SELECT * FROM `stock_out` WHERE `STOCK_OUT_ID` LIKE '%$search%' AND `STATUS` = 'Active'";
            } else {
                if ($branch !== 'all' && $emp_id !== 'all') {
                    $response_sql = "SELECT * FROM `stock_out` WHERE `BRANCH_ID` = '$branch' AND `EMP_ID` = '$emp_id' AND `STATUS` = 'Active'";
                } elseif ($branch == 'all' && $emp_id !== 'all') {
                    $response_sql = "SELECT * FROM `stock_out` WHERE `EMP_ID` = '$emp_id' AND `STATUS` = 'Active'";
                } elseif ($branch !== 'all' && $emp_id == 'all') {
                    $response_sql = "SELECT * FROM `stock_out` WHERE `BRANCH_ID` = '$branch' AND `STATUS` = 'Active'";
                } else {
                    $response_sql = "SELECT * FROM `stock_out` WHERE `STATUS` = 'Active'";
                }
            }

            if ($response_result = $conn->query($response_sql)) {
                if ($response_result->num_rows > 0) {
                    while ($response_row = $response_result->fetch_assoc()) {
?>
                        <tr>
                            <td><?= $response_row['STOCK_OUT_ID'] ?></td>
                            <td>
                                <?php
                                $branch_sql = $conn->query("SELECT `BRANCH` FROM `branch` WHERE `ID` = '{$response_row['BRANCH_ID']}'");
                                if ($branch_sql) {
                                    $branch_result = $branch_sql->fetch_assoc();
                                    echo $branch_result['BRANCH'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $emp_sql = $conn->query("SELECT * FROM `employee` WHERE `EMP_ID` = '{$response_row['EMP_ID']}'");
                                if ($emp_sql) {
                                    $emp_result = $emp_sql->fetch_assoc();
                                    echo $emp_result['FIRST_NAME'] . ' ' . $emp_result['MIDDLE_INITIAL'] . ' ' . $emp_result['LAST_NAME'];
                                }
                                ?>
                            </td>
                            <td><?= $response_row['DATE'] ?></td>
                            <td><?= $response_row['TOTAL'] ?></td>
                            <td class="btns">
                                <a href="stock-out-details.php?id=<?= $response_row['STOCK_OUT_ID'] ?>"><i class="fa-solid fa-eye"></i></a>
                                <button type="button" id="btn-edit-stock-out" data-id="<?= $response_row['STOCK_OUT_ID'] ?>" data-branch="<?= $response_row['BRANCH_ID'] ?>" data-date="<?= $response_row['DATE'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" id="btn-delete-stock-out" data-id="<?= $response_row['STOCK_OUT_ID'] ?>"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">
                            <center>No Stock Out Found</center>
                        </td>
                    </tr>
<?php
                }
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
    header("Location: ../index.php");
    exit();
}
