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

    if ($emp_type === 'Admin' && $emp_status === 'active') {
?>
        <div class="specific-element1">
            <center>
                <i class="fa-solid fa-person"></i>
                <h4 class="text-dark">
                    <?php
                    $sales_sql = "SELECT * FROM sales WHERE DATE = '$currentDate'";
                    $sales_result = $conn->query($sales_sql);
                    if ($sales_result->num_rows > 0) {
                        echo $sales_result->num_rows;
                    } else {
                        echo 0;
                    }
                    ?>
                </h4>
                <h5>No of Sales Today</h5>
            </center>
        </div>

        <div class="specific-element2">
            <center>
                <i class="fa-solid fa-money-bill"></i>
                <h4 class="text-dark">
                    <?php
                    $sales_sql = "SELECT TOTAL FROM sales WHERE DATE = '$currentDate'";
                    $sales_result = $conn->query($sales_sql);
                    $total = 0;
                    if ($sales_result->num_rows > 0) {
                        while($sales = $sales_result->fetch_assoc()){
                            $total += $sales['TOTAL'];
                        }
                        echo '₱ '.$total;
                    } else {
                        echo '₱ '.$total;
                    }
                    ?>
                </h4>
                <h5>Today's Sales</h5>
            </center>
        </div>

        <div class="specific-element3">
            <center>
                <i class="fa-solid fa-cart-shopping"></i>
                <h4 class="text-dark">
                    <?php 
                    $order_sql = "SELECT * FROM `order` WHERE STATUS = 'Waiting'";
                    $order_result = $conn->query($order_sql);
                    if($order_result->num_rows > 0){
                        echo $order_result->num_rows;
                    } else {
                        echo 0;
                    }
                    ?>
                </h4>
                <h5>Pending Order</h5>
            </center>
        </div>
<?php
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