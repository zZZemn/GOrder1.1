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
        if (isset($_POST['cust_type']) && isset($_POST['search'])) {
            $cust_type = $_POST['cust_type'];
            $search = $_POST['search'];

            if ($search === '') {
                if($cust_type === ''){
                    $cust_sql = "SELECT * FROM customer_user";
                } else {
                    $cust_sql = "SELECT * FROM customer_user WHERE DISCOUNT_TYPE = '$cust_type'";
                }
            } else {
                $cust_sql = "SELECT * FROM customer_user WHERE FIRST_NAME LIKE '%$search%' OR LAST_NAME LIKE '%$search%' OR CUST_ID LIKE '%$search%'";
            }

            if ($cust_result = $conn->query($cust_sql)) {
                if ($cust_result->num_rows > 0) {
                    while ($cust = $cust_result->fetch_assoc()) {
?>
                        <tr>
                            <td><?php echo $cust['CUST_ID'] ?></td>
                            <td><?php echo $cust['FIRST_NAME'] . ' ' . $cust['MIDDLE_INITIAL'] . ' ' . $cust['LAST_NAME'] . ' ' . $cust['SUFFIX'] ?></td>
                            <td><?php echo $cust['CONTACT_NO'] ?></td>
                            <td><?php echo $cust['DISCOUNT_TYPE'] ?></td>
                            <td class="actions-btn">
                                <a href="#" class="btn btn-primary text-light"><i class="fa-regular fa-pen-to-square"></i></a>
                                <a href="#" id="change-status" data-cust_id="<?php echo $cust['CUST_ID'] ?>" data-new_status="<?php echo ($cust['STATUS'] === 'active') ? 'deact' : 'active' ?>" class="btn <?php echo ($cust['STATUS'] === 'active') ? 'btn-danger' : 'btn-success' ?> ?>"><?php echo ($cust['STATUS'] === 'active') ? 'Deactivate' : 'Activate' ?></a>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                        <tr>
                            <td colspan="5">
                                <center>No Customer Found</center>
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
