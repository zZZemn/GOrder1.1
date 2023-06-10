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
                        <tr class="bg-success region-tr">
                            <td>
                                <form class="edit-region">
                                    <input type="text" class="form-control" value="<?php echo $row['REGION'] ?>" maxlength="12">
                                    <a href="#" class="edit-region-btn btn btn-light"><i class="fa-regular fa-pen-to-square"></i></a>
                                </form>
                            </td>
                            <td class="add-province-td">
                                <form class="add-province">
                                    <input type="hidden" value="<?php echo $region_id ?>" id="region_id" class="region_id">
                                    <input type="text" class="form-control txt-add-province" placeholder="Add new Province in <?php echo $row['REGION'] ?>" id="txt-add-province">
                                    <input type="submit" class="btn btn-light submit-province btn-add-province" id="btn-add-province" value="Add">
                                </form>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
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
                             while ($province_row = $provinces_result->fetch_assoc()) {
                                $province_id = $province_row['PROVINCE_ID'];

                                ?>

                                        <tr class="provinces-table-tr">
                                            <td colspan="2">
                                                <table class="table table-stripe province-table">
                                                    <tr>
                                                        <th colspan="2">
                                                            <center><?php echo $province_row['PROVINCE'] ?></center>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                       <td class="">
                                                           <form class="edit-province">
                                                               <input type="text" class="form-control" value="<?php echo $province_row['PROVINCE'] ?>">
                                                               <a href="#" class="btn btn-success btn-edit-province"><i class="fa-regular fa-pen-to-square"></i></a>
                                                           </form>
                                                       </td>
                                                           <td>
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
                                                    ?>
                                                        <tr class="provinces-tr-center">
                                                            <td colspan="2" class="bg-warning text-light municipalilty-td">
                                                                <center class="municipality-center">Municipality In <?php echo $province_row['PROVINCE'] ?></center>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                        while ($municipality_row = $municipality_result->fetch_assoc()) {
                                                            $municipality_id = $municipality_row['MUNICIPALITY_ID'];
                                                            ?>
                                                        <tr>
                                                            <td colspan="2">
                                                                <table class="table table-stripe municipality-table">
                                                                    <tr>
                                                                        <th colspan="2"><?php echo $municipality_row['MUNICIPALITY'] ?></th>
                                                                    </tr>
                                                            
                                                                    <tr class="">
                                                                        <td>
                                                                            <form class="form-edit-municipality">
                                                                                <input type="text" class="form-control" value="<?php echo $municipality_row['MUNICIPALITY'] ?>">
                                                                                <a href="#" class="btn-edit-municipality btn btn-warning"><i class="fa-regular fa-pen-to-square"></i></a>
                                                                            </form>
                                                                        </td>
                                                                        <td>
                                                                            <form class="add-barangay">
                                                                                <input type="hidden" value="<?php echo $municipality_id ?>" id="municipality_id" class="municipality_id">
                                                                                <input type="text" class="form-control txt-add-barangay" placeholder="Add New Barangay in <?php echo $municipality_row['MUNICIPALITY'] ?>" id="txt-add-barangay">
                                                                                <input type="number" class="form-control txt-df" placeholder="Delivery Fee">
                                                                                <input type="submit" class="btn btn-warning text-light submit-barangay btn-add-barangay" id="btn-add-barangay">
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                $barangay_sql = "SELECT * FROM barangay WHERE MUNICIPALITY_ID = '$municipality_id'";
                                                                $barangay_result = $conn->query($barangay_sql);
                                                                if ($barangay_result->num_rows > 0) {
                                                                    ?>
                                                                    <tr class="provinces-tr-center">
                                                                        <td colspan="2" class="bg-danger text-light">
                                                                            <center>Barangay In <?php echo $municipality_row['MUNICIPALITY'] ?></center>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <table class="table table-striped barangay-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Barangay</th>
                                                                                        <th>Delivery Fee</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    while ($barangay_row = $barangay_result->fetch_assoc()) { ?>


                                                                                        <tr>
                                                                                            <td><input type="text" class="form-control" value="<?php echo $barangay_row['BARANGAY'] ?>"></td>
                                                                                            <td><input type="text" class="form-control" value="<?php echo $barangay_row['DELIVERY_FEE'] ?>"></td>
                                                                                            <td class="bgy-edit-btn"><a href="#" class="btn btn-primary"><i class="fa-regular fa-pen-to-square"></i></a><a href="#" class="btn btn-primary"><i class="fa-solid fa-trash"></i></a></td>
                                                                                        </tr>
                                                                                    <?php } ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>

                                                                    <?php
                                                                } else {
                                                                    //no barangay found
                                                                }
                                                                ?>
                                                                </table>
                                                            </td>

                                                            <?php
                                                        }
                                                    ?>
                                                    <?php
                                                    } else {
                                                        //no municipality found
                                                        ?>
                                                            <tr>
                                                                <td>No Municipalilty Found</td>
                                                            </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </table>
                                            </td>
                                        </tr>

                                <?php
                             }
                             
                        } else {
                            //no province found
                        }
                        ?>
                    </tbody>
                </table>

    <?php
            }
        } else {
            //no address found
        }
    } else {
        //access deny
    }
} else {
    //go to login
}
