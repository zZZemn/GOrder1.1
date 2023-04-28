<?php 
    session_start();
    if(isset($_SESSION['id']))
    {
        include('../database/db.php');
        include('../time-date.php');

        $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
        $result  = $conn->query($sql);
        $emp = $result->fetch_assoc();
        if(isset($emp) && $emp['EMP_TYPE'] == "Admin" && $emp['EMP_STATUS'] == "active")
        {
            $empID = $emp['EMP_ID'];
            if(isset($_GET['sup_id']))
            {
                $sup_id = $_GET['sup_id'];

                $sup_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $sup_id";
                $sup_sql_result = $conn->query($sup_sql);
                $supplier = $sup_sql_result->fetch_assoc();

                $sup_name = $supplier['NAME'];

                $sql = "UPDATE `supplier` SET `SUPPLIER_STATUS`= 'deleted' WHERE SUPPLIER_ID = $sup_id";

                $delete_sup_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                                                VALUES ('$empID','Delete $sup_name from supplier list.','$delDate','$delTime')";

                if($conn->query($sql) === true && $conn->query($delete_sup_log) === true)
                {
                    header("Location: ../admin/products-supplier.php?status=deletion_success");
                    exit;
                }
                else
                {
                    header("Location: ../admin/products-supplier.php?status=deletion_not_success");
                    exit;
                }
            }
            else
            {
                echo "<title>Access Denied</title>
            <div class='access-denied'>
                <h1>Access Denied</h1>
                <h5>Sorry, you are not authorized to access this page.</h5>
            </div>";
            }
        }
        else
        {
            echo "<title>Access Denied</title>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Sorry, you are not authorized to access this page.</h5>
                    </div>";
        }
    }
    else
    {
        echo "<title>Access Denied</title>
            <div class='access-denied'>
                <h1>Access Denied</h1>
                <h5>Sorry, you are not authorized to access this page.</h5>
            </div>";
    }

?>
<html>
    <head>
        <title>Deleting Product</title>
        <link href="../css/access-denied.css" rel="stylesheet">
    </head>
</html>