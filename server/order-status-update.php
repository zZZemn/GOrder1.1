<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $transactionID = $_GET['id'];
            $orders_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transactionID'";
            $orders_result = $conn->query($orders_sql);
            if ($orders_result->num_rows > 0) {
                $order = $orders_result->fetch_assoc();
                $order_status = $order['STATUS'];
                $order_type = $order['DELIVERY_TYPE'];

                if ($order_type === 'Deliver') {
                    $progressBar_percentage = 0;
                    $circle_percentage = 0;
                    if ($order_status === 'Waiting') {
                        $progressBar_percentage = 0;
                        $circle_percentage = 0;
                    } elseif ($order_status === 'Accepted') {
                        $progressBar_percentage = 25;
                        $circle_percentage = 23;
                    } elseif ($order_status === 'For-Delivery') {
                        $progressBar_percentage = 50;
                        $circle_percentage = 48;
                    } elseif ($order_status === 'Shipped') {
                        $progressBar_percentage = 75;
                        $circle_percentage = 73;
                    } elseif ($order_status === 'Delivered') {
                        $progressBar_percentage = 100;
                        $circle_percentage = 98;
                    } else {
                        $progressBar_percentage = 0;
                        $circle_percentage = 0;
                    }
?>
                    <div class="progress" style="height: 8px; width: 600px">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progressBar_percentage ?>%;" aria-valuenow="<?php echo $progressBar_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress-circle">
                        <div class="circle bg-primary" style=" left: <?php echo $circle_percentage ?>%;"><span class="bg-primary"><?php echo $order_status ?></span></div>
                    </div>
                    <div class="progress-photo-container">
                        <i class="fa-solid fa-location-dot bg-primary text-light" id="waiting"></i>
                        <i class="fa-solid fa-check bg-primary text-light" id="accepted"></i>
                        <i class="fa-solid fa-box-open bg-primary text-light" id="for-delivery"></i>
                        <i class="fa-solid fa-motorcycle bg-primary text-light" id="shipped"></i>
                        <i class="fa-solid fa-house-circle-check bg-primary text-light" id="delivered"></i>
                    </div>
                <?php
                } elseif ($order_type === 'Pick Up') {
                    $progressBar_percentage = 0;
                    $circle_percentage = 0;
                    if ($order_status === 'Waiting') {
                        $progressBar_percentage = 0;
                        $circle_percentage = 0;
                    } elseif ($order_status === 'Accepted') {
                        $progressBar_percentage = 37;
                        $circle_percentage = 35;
                    } elseif ($order_status === 'Ready To Pick Up') {
                        $progressBar_percentage = 70;
                        $circle_percentage = 68;
                    } elseif ($order_status === 'Picked Up') {
                        $progressBar_percentage = 100;
                        $circle_percentage = 98;
                    } else {
                        $progressBar_percentage = 0;
                        $circle_percentage = 0;
                    }
                ?>
                    <div class="progress" style="height: 8px; width: 600px">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progressBar_percentage ?>%;" aria-valuenow="<?php echo $progressBar_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress-circle">
                        <div class="circle bg-primary" style=" left: <?php echo $circle_percentage ?>%;"><span class="bg-primary"><?php echo $order_status ?></span></div>
                    </div>
                    <div class="progress-photo-container">
                        <i class="fa-solid fa-location-dot bg-primary text-light" id="waiting"></i>
                        <i class="fa-solid fa-check bg-primary text-light" id="accepted-pickup"></i>
                        <i class="fa-solid fa-box-open bg-primary text-light" id="ready-to-pickup"></i>
                        <i class="fa-solid fa-user-check bg-primary text-light" id="picked-up"></i>
                    </div>
<?php
                } else {
                    echo "
                    <head>
                        <link rel='stylesheet' href='../css/access-denied.css'>
                    </head>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Delivery Type not available.</h5>
                    </div>";
                }
            } else {
                echo "
                    <head>
                        <link rel='stylesheet' href='../css/access-denied.css'>
                    </head>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Transaction ID not found.</h5>
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
