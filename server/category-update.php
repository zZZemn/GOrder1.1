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
        $categories_sql = "SELECT * FROM category ORDER BY CAT_NAME";
        $categories_result = $conn->query($categories_sql);
        if ($categories_result->num_rows > 0) {
            while ($cat_row = $categories_result->fetch_assoc()) {
                $cat_id = $cat_row['CAT_ID'];
?>
                <div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="cat_name">
                                    <input type="text" class="cat_name_edit form-control" value="<?php echo $cat_row['CAT_NAME'] ?>" maxlength="20">
                                </th>
                                <td><a href="#" class="cat_name_edit_btn <?php echo $cat_row['CAT_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a></td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <center>Sub Categories</center>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subcat_sql = "SELECT * FROM sub_category WHERE CAT_ID = $cat_id ORDER BY SUB_CAT_NAME";
                            $subcat_result = $conn->query($subcat_sql);
                            if ($subcat_result->num_rows > 0) {
                                while ($subcat_row = $subcat_result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td><input type="text" class="sub_cat_edit form-control" value="<?php echo $subcat_row['SUB_CAT_NAME'] ?>" maxlength="20"></td>
                                        <td><a href="#" class="sub_cat_edit_btn <?php echo $subcat_row['SUB_CAT_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a></td>
                                    </tr>

                            <?php
                                }
                            }
                            ?>
                            <tr>
                                <td class="add-subcat">
                                    <form>
                                        <input type="hidden" name="cat_id" id="cat-id" value="<?php echo $cat_id ?>">
                                        <input type="text" name="add_sub_cat" id="add_sub_cat" class="form-control" placeholder="Add Sub-Category" maxlength="20">
                                </td>
                                <td class="add-subcat">
                                    <input type="submit" name="submit_new_cat" class="btn btn-primary" id="btn-add-subcat" value="Add">
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php
            }
        } else {
            ?>
            <center class="no-cat-found">
                <h5>No Category Found</h5>
            </center>
<?php
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
