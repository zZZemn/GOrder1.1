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
                                <td class="bgys-btn">
                                    <a href="#" class="btn btn-primary" id="edit-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>" data-df="<?php echo $bgy['DELIVERY_FEE'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <a href="#" class="btn btn-danger" id="disable-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>">Disable</a>
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
                $noAddFound = true;
                $prov_id = $_POST['province'];
                $prov_result = $conn->query("SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$prov_id'");
                $prov = $prov_result->fetch_assoc();

                $region_id = $prov['REGION_ID'];
                $province = $prov['PROVINCE'];

                $region_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
                $reg = $region_result->fetch_assoc();
                $region = $reg['REGION'];

                // 
                $municipality_sql = "SELECT MUNICIPALITY_ID, MUNICIPALITY FROM municipality WHERE PROVINCE_ID = '$prov_id'";
                $municipality_result = $conn->query($municipality_sql);
                if ($municipality_result->num_rows > 0) {
                    while ($muni_row = $municipality_result->fetch_assoc()) {
                        $muni_id = $muni_row['MUNICIPALITY_ID'];
                        $municipality = $muni_row['MUNICIPALITY'];

                        $barangay_sql = "SELECT * FROM barangay WHERE MUNICIPALITY_ID = '$muni_id' AND BARANGAY_STATUS = 'active'";
                        $barangay_result = $conn->query($barangay_sql);
                        if ($barangay_result->num_rows > 0) {
                            $noAddFound = false;
                            while ($bgy = $barangay_result->fetch_assoc()) {
                    ?>
                                <tr>
                                    <td><?php echo $region ?></td>
                                    <td><?php echo $province ?></td>
                                    <td><?php echo $municipality ?></td>
                                    <td><?php echo $bgy['BARANGAY'] ?></td>
                                    <td><?php echo $bgy['DELIVERY_FEE'] ?></td>
                                    <td class="bgys-btn">
                                        <a href="#" class="btn btn-primary" id="edit-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>" data-df="<?php echo $bgy['DELIVERY_FEE'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a href="#" class="btn btn-danger" id="disable-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>">Disable</a>
                                    </td>
                                </tr>
                    <?php
                            }
                        }
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

                if ($noAddFound == true) {
                ?>
                    <tr>
                        <td colspan="6">
                            <center>No Address Found</center>
                        </td>
                    </tr>
                    <?php
                }
            } elseif ($_POST['region'] !== '') {
                $noAddFound = true;
                $region_id = $_POST['region'];
                $reg_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
                $reg = $reg_result->fetch_assoc();
                $region = $reg['REGION'];

                $province_sql = "SELECT PROVINCE, PROVINCE_ID FROM province WHERE REGION_ID = '$region_id'";
                $province_result = $conn->query($province_sql);
                if ($province_result->num_rows > 0) {
                    while ($prov = $province_result->fetch_assoc()) {
                        $province = $prov['PROVINCE'];
                        $province_id = $prov['PROVINCE_ID'];

                        $municipality_sql = "SELECT MUNICIPALITY, MUNICIPALITY_ID FROM municipality WHERE PROVINCE_ID = '$province_id'";
                        $municipality_result = $conn->query($municipality_sql);
                        if ($municipality_result->num_rows > 0) {
                            while ($muni = $municipality_result->fetch_assoc()) {
                                $municipality = $muni['MUNICIPALITY'];
                                $muni_id = $muni['MUNICIPALITY_ID'];

                                $barangay_sql = "SELECT * FROM barangay WHERE MUNICIPALITY_ID = '$muni_id' AND BARANGAY_STATUS = 'active'";
                                $barangay_result = $conn->query($barangay_sql);
                                if ($barangay_result->num_rows > 0) {
                                    $noAddFound = false;
                                    while ($bgy = $barangay_result->fetch_assoc()) {
                    ?>
                                        <tr>
                                            <td><?php echo $region ?></td>
                                            <td><?php echo $province ?></td>
                                            <td><?php echo $municipality ?></td>
                                            <td><?php echo $bgy['BARANGAY'] ?></td>
                                            <td><?php echo $bgy['DELIVERY_FEE'] ?></td>
                                            <td class="bgys-btn">
                                                <a href="#" class="btn btn-primary" id="edit-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>" data-df="<?php echo $bgy['DELIVERY_FEE'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                                <a href="#" class="btn btn-danger" id="disable-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>">Disable</a>
                                            </td>
                                        </tr>
                    <?php
                                    }
                                }
                            }
                        }
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
                if ($noAddFound === true) {
                ?>
                    <tr>
                        <td colspan="6">
                            <center>No Address Found</center>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_STATUS = 'active'";
                $bgy_result = $conn->query($bgy_sql);
                if ($bgy_result->num_rows > 0) {
                    while ($bgy = $bgy_result->fetch_assoc()) {
                        $muni_id = $bgy['MUNICIPALITY_ID'];
                        $muni_result = $conn->query("SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'");
                        $muni = $muni_result->fetch_assoc();

                        $municipality = $muni['MUNICIPALITY'];
                        $prov_id = $muni['PROVINCE_ID'];

                        $prov_result = $conn->query("SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$prov_id'");
                        $prov = $prov_result->fetch_assoc();

                        $province = $prov['PROVINCE'];
                        $region_id = $prov['REGION_ID'];

                        $region_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
                        $reg = $region_result->fetch_assoc();
                        $region = $reg['REGION'];
                    ?>
                        <tr>
                            <td><?php echo $region ?></td>
                            <td><?php echo $province ?></td>
                            <td><?php echo $municipality ?></td>
                            <td><?php echo $bgy['BARANGAY'] ?></td>
                            <td><?php echo $bgy['DELIVERY_FEE'] ?></td>
                            <td class="bgys-btn">
                                <a href="#" class="btn btn-primary" id="edit-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>" data-df="<?php echo $bgy['DELIVERY_FEE'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                <a href="#" class="btn btn-danger" id="disable-bgy" data-bgy_id="<?php echo $bgy['BARANGAY_ID'] ?>" data-bgy="<?php echo $bgy['BARANGAY'] ?>">Disable</a>
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
