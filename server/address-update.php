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
                                <form method="post" action="" class="add-province">
                                    <input type="text" class="form-control" placeholder="Add new Province in <?php echo $row['REGION'] ?>">
                                    <input type="submit" class="btn btn-primary submit-province">
                                </form>
                            </td>
                        </tr>
                        <?php
                        $provinces_sql = "SELECT * FROM province WHERE REGION_ID = '$region_id'";
                        $provinces_result = $conn->query($provinces_sql);
                        if ($provinces_result->num_rows > 0) {
                        ?>
                            <tr>
                                <?php
                                while ($province_row = $provinces_result->fetch_assoc()) {
                                    $province_id = $province_row['PROVINCE_ID'];
                                ?>
                                    <td><input type="text" class="form-control" value="<?php $province_row['PROVINCE'] ?>"></td>
                                    <td><a href="#"><i class="fa-regular fa-pen-to-square"></i></a></td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="no-province-found-tr">
                                <td class="no-province-found" colspan="2">
                                    <center>No Province Found</center>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                } else {
                    ?>
                    <center class="no-region-found" colspan="2">
                        <h5>No Address Found</h5>
                    </center>
                <?php
                }

                ?>
                    </tbody>
                </table>
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