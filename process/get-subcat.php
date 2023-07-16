<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
    if (isset($emp) && $emp['EMP_TYPE'] == "Admin" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $cat_id = $_GET['id'];

            $sub_cat_sql = "SELECT * FROM sub_category WHERE CAT_ID = '$cat_id'";
            if (($sub_cat_result = $conn->query($sub_cat_sql)) !== FALSE) {
                if ($sub_cat_result->num_rows > 0) {
                    while ($sub_cat = $sub_cat_result->fetch_assoc()) {
?>
                        <option value="<?php echo $sub_cat['SUB_CAT_ID'] ?>"><?php echo $sub_cat['SUB_CAT_NAME'] ?></option>
<?php
                    }
                } else {
                    echo 'Empty';
                }
            } else {
                echo "query unsuccessfl";
            }
        }
    } else {
        echo "<title>Access Denied</title>
                    <div class='access-denied'>
                        <h1>Access Denied</h1>
                        <h5>Sorry, you are not authorized to access this page.</h5>
                    </div>";
    }
} else {
    header("Location: ../index.php");
    exit;
}
