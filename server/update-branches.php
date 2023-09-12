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
        if (isset($_GET['getBranches'])) {
            $branches_sql = "SELECT * FROM `branch`";
            if ($branches_result = $conn->query($branches_sql)) {
                if ($branches_result->num_rows > 0) {
                    while ($branches_row = $branches_result->fetch_assoc()) {
?>
                        <tr>
                            <td class="<?= ($branches_row['STATUS'] !== 'Active') ? 'text-danger' : '' ?>"><?= $branches_row['ID'] ?></td>
                            <td class="<?= ($branches_row['STATUS'] !== 'Active') ? 'text-danger' : '' ?>"><?= $branches_row['BRANCH'] ?></td>
                            <td>
                                <button data-id="<?= $branches_row['ID'] ?>" data-name="<?= $branches_row['BRANCH'] ?>" class="open-edit-branch btn btn-dark"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button data-id="<?= $branches_row['ID'] ?>" <?= ($branches_row['STATUS'] == 'Active') ? 'class="deactivate-branch btn btn-danger" data-action="Deactivated"' : 'class="deactivate-branch btn btn-success" data-action="Active"' ?>><?= ($branches_row['STATUS'] == 'Active') ? 'Disable' : 'Enable' ?></button>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3">
                            <center>No Branch Found.</center>
                        </td>
                    </tr>
<?php
                }
            } else {
                echo '404';
            }
        } else {
            echo '404';
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
