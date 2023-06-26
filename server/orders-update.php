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

    if(isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $orders_sql = "SELECT * FROM `order` WHERE STATUS = '$filter' ORDER BY `DATE` DESC, `TIME` DESC";
            $orders_result = $conn->query($orders_sql);
            if ($orders_result->num_rows > 0) {
                while ($order_row = $orders_result->fetch_assoc()) {
                    $custID = $order_row['CUST_ID'];
                    $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$custID'";
                    $cust_result = $conn->query($cust_sql);
                    $cust = $cust_result->fetch_assoc();

                    $unit_st = $order_row['UNIT_STREET'];
                    $bgy_id = $order_row['BARANGAY_ID'];

                    $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                    $bgy_result = $conn->query($bgy_sql);
                    $bgy = $bgy_result->fetch_assoc();

                    $barangay = $bgy['BARANGAY'];
                    $muni_id = $bgy['MUNICIPALITY_ID'];

                    $muni_sql = "SELECT * FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'";
                    $muni_result = $conn->query($muni_sql);
                    $muni = $muni_result->fetch_assoc();

                    $municipality = $muni['MUNICIPALITY'];
                    $prov_id = $muni['PROVINCE_ID'];

                    $province_sql = "SELECT * FROM province WHERE PROVINCE_ID = '$prov_id'";
                    $province_result = $conn->query($province_sql);
                    $prov = $province_result->fetch_assoc();

                    $province = $prov['PROVINCE'];
                    $reg_id = $prov['REGION_ID'];

                    $region_sql = "SELECT * FROM region WHERE REGION_ID = '$reg_id'";
                    $region_result = $conn->query($region_sql);
                    $reg = $region_result->fetch_assoc();

                    $region = $reg['REGION'];

                    $full_address = $unit_st . ", " . $barangay . ", " . $municipality . ", " . $province . ", " . $region;

                    $order_on = $order_row['DATE'] . " - " . date("h:i A", strtotime($order_row['TIME']));

?>
                    <tr>
                        <td><a href="order-details.php?transaction_id=<?php echo $order_row['TRANSACTION_ID'] ?>" target="_blank"><?php echo $order_row['TRANSACTION_ID'] ?></a></td>
                        <td><?php echo $cust['FIRST_NAME'] . " " . $cust['LAST_NAME'] ?></td>
                        <td><?php echo $order_row['PAYMENT_TYPE'] ?></td>
                        <td><?php echo $order_row['DELIVERY_TYPE'] ?></td>
                        <td><?php echo $full_address ?></td>
                        <td><?php echo $order_on ?></td>
                        <td><?php echo $order_row['TOTAL'] ?></td>
                        <td><?php echo $order_row['STATUS'] ?></td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <th colspan="8">
                        <center>No Order/s Found (<?php echo $filter ?>)</center>
                    </th>
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
?>