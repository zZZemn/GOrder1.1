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
        if (isset($_POST['region']) && isset($_POST['province']) && isset($_POST['municipality'])) {
            if ($_POST['municipality'] !== '') {
                $muni_id = $_POST['municipality'];
                $muni_result = $conn->query("SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'");
                if ($muni_result->num_rows > 0) {
                    $muni = $muni_result->fetch_assoc();
                    $pro_id = $muni['PROVINCE_ID'];
                    $municipality = $muni['MUNICIPALITY'];

                    $prov_result = $conn->query("SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$pro_id'");
                    $prov = $prov_result->fetch_assoc();
                    $region_id = $prov['REGION_ID'];
                    $province = $prov['PROVINCE'];

                    $region_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
                    $reg = $region_result->fetch_assoc();
                    $region = $reg['REGION'];

                    $bgy_sql = "SELECT * FROM barangay WHERE MUNICIPALITY_ID = '$muni_id' AND BARANGAY_STATUS = 'active'";
                    $bgy_result = $conn->query($bgy_sql);
                    if ($bgy_result->num_rows > 0) {
                        while ($bgy = $bgy_result->fetch_assoc()) {
?>
                            <tr>
                                <td><?php echo $region ?></td>
                                <td><?php echo $province ?></td>
                                <td><?php echo $municipality ?></td>
                                <td><?php echo $bgy['BARANGAY'] ?></td>
                                <td><?php echo $bgy['DELIVERY_FEE'] ?></td>
                                <td>Action</td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6">
                                <center>No Address Found</center>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">
                            <center>No Address Found</center>
                        </td>
                    </tr>
<?php
                }
            } elseif ($_POST['province'] !== '') {
                
            } elseif ($_POST['region'] !== '') {
            } else {
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
