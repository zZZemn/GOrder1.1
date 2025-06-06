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

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['search'])) {
            $return_sql = "SELECT * FROM `return` WHERE `STATUS` = 'Pending'";
            $return_result = $conn->query($return_sql);
            if ($return_result->num_rows > 0) {
                while ($row = $return_result->fetch_assoc()) {
?>
                    <tr>
                        <td><?= $row['TRANSACTION_ID'] ?></td>
                        <td><?= $row['RETURN_DATE'] ?></td>
                        <td><?= $row['RETURN_AMOUNT'] ?></td>
                        <td><?= $row['RETURN_REASON'] ?></td>
                        <td>
                            <a href="return-request-details.php?id=<?= $row['RETURN_ID'] ?>" class="btn btn-dark" target="_blank">View <i class='fa-solid fa-eye'></i></a>
                        </td>
                    </tr>
<?php
                }
            } else {
                ?>
                    <tr>
                        <td colspan="5"><center>No Request Found</center></td>
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
