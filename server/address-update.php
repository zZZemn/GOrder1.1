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

        $regions_sql = "SELECT * FROM region WHERE REGION_STATUS = 'active'";
        $regions_result = $conn->query($regions_sql);
        if ($regions_result->num_rows > 0) {
            while ($row = $regions_result->fetch_assoc()) {
                $region_id = $row['REGION_ID'];
?>

                <table class="table table-striped address-table">
                    <thead>
                        <tr>
                            <th><input type="text" class="form-control" value="<?php echo $row['REGION'] ?>" maxlength="12"></th>
                            <td><a href="#"><i class="fa-regular fa-pen-to-square"></i></a></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="add-province-tr">
                            <td colspan="2" class="add-province-td">
                                <form class="add-province">
                                    <input type="hidden" value="<?php echo $region_id ?>" id="region_id" class="region_id">
                                    <input type="text" class="form-control txt-add-province" placeholder="Add new Province in <?php echo $row['REGION'] ?>" id="txt-add-province">
                                    <input type="submit" class="btn btn-primary submit-province btn-add-province" id="btn-add-province">
                                </form>
                            </td>
                        </tr>

                        <?php
                        $provinces_sql = "SELECT * FROM province WHERE REGION_ID = '$region_id'";
                        $provinces_result = $conn->query($provinces_sql);
                        if ($provinces_result->num_rows > 0) {
                        ?>
                            <tr class="provinces-tr-center">
                                <td colspan="2" class="bg-success text-light">
                                    <center>Provinces In <?php echo $row['REGION'] ?></center>
                                </td>
                            </tr>
                            <?php
                            ?>

                            <?php
                            while ($province_row = $provinces_result->fetch_assoc()) {
                                $province_id = $province_row['PROVINCE_ID'];
                            ?>
                                <tr class="provinces-table">
                                    <td><input type="text" class="form-control" value="<?php echo $province_row['PROVINCE'] ?>"></td>
                                    <td><a href="#"><i class="fa-regular fa-pen-to-square"></i></a></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <form class="add-municipality">
                                            <input type="hidden" value="<?php echo $province_id ?>" id="province_id" class="province_id">
                                            <input type="text" class="form-control txt-add-municipality" placeholder="Add new Municipality in <?php echo $province_row['PROVINCE'] ?>" id="txt-add-municipality">
                                            <input type="submit" class="btn btn-success submit-municipality btn-add-municipality" id="btn-add-municipality">
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                $municipality_sql = "SELECT * FROM MUNICIPALITY WHERE PROVINCE_ID = '$province_id'";
                                $municipality_result = $conn->query($municipality_sql);
                                if ($municipality_result->num_rows > 0) {
                                    while ($municipality_row = $municipality_result->fetch_assoc()) {
                                ?>
                                        <tr class="municipality-table">
                                            <td><input type="text" class="form-control" value="<?php echo $municipality_row['MUNICIPALITY'] ?>"></td>
                                            <td><a href="#"><i class="fa-regular fa-pen-to-square"></i></a></td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2" class="bg-success">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <center>No Municipality Found in <?php echo $province_row['PROVINCE'] ?></center>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        <?php
                        } else {
                        ?>
                            <tr>
                                <td colspan="2">
                                    <center>No Province Found in <?php echo $row['REGION'] ?></center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                        <?php
                        ?>
                    </tbody>
                </table>
            <?php
            }
        } else {
            ?>
            <center class="no-region-found" colspan="2">
                <h5>No Address Found</h5>
            </center>
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
    header("Location: ../index.php");
    exit();
}
?>