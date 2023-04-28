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
            if(isset($_GET['product_id']))
            {
                $product_id = $_GET['product_id'];

                $product_img_sql = "SELECT * FROM products WHERE PRODUCT_ID = $product_id";
                $product_img_result = $conn->query($product_img_sql);
                $product_img = $product_img_result->fetch_assoc();
                $product_img_name = $product_img['PRODUCT_IMG'];

                $product_name = $product_img['PRODUCT_NAME'];
                $delTime = $currentTime;
                $delDate = $currentDate;
                $empID = $emp['EMP_ID'];

                $sql = "UPDATE `products` SET `PRODUCT_STATUS`= 'deleted' WHERE PRODUCT_ID = $product_id";

                $delete_pro_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                                                VALUES ('$empID','Delete $product_name from product list.','$delDate','$delTime')";

                if($conn->query($sql) === true && $conn->query($delete_pro_log) === true)
                {
                    if($product_img_name != "product_default_img.png")
                    {
                        $file = '../img/products/'.$product_img_name;
                        if (file_exists($file)) {
                            unlink($file);
                        }
                    }
                    header("Location: ../admin/products-allproducts.php?status=deletion_success");
                    exit;
                }
                else
                {
                    header("Location: ../admin/products-allproducts.php?status=deletion_not_success");
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