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
        if (isset($_GET['filter']) && isset($_GET['search'])) {
            $filter = $_GET['filter'];
            $search = $_GET['search'];

            if ($search !== '') {
                if($filter === 'all'){
                    $emp_sql = "SELECT * FROM employee WHERE (FIRST_NAME LIKE '%$search%' OR LAST_NAME LIKE '%$search%' OR EMP_ID LIKE '%$search%' OR ADDRESS LIKE '%$search%') AND EMP_STATUS = 'active'";
                } else {
                    $emp_sql = "SELECT * FROM employee WHERE EMP_TYPE = '$filter' AND (FIRST_NAME LIKE '%$search%' OR LAST_NAME LIKE '%$search%' OR EMP_ID LIKE '%$search%' OR ADDRESS LIKE '%$search%') AND EMP_STATUS = 'active'";
                }
            } else {
                if($filter === 'all'){
                    $emp_sql = "SELECT * FROM employee WHERE EMP_STATUS = 'active'";
                } else {
                    $emp_sql = "SELECT * FROM employee WHERE EMP_TYPE = '$filter' AND EMP_STATUS = 'active'";
                }
            }

            $emp_result = $conn->query($emp_sql);
            if ($emp_result->num_rows > 0) {
                while ($emp_row = $emp_result->fetch_assoc()) {
?>
                    <tr>
                        <td><?php echo $emp_row['LAST_NAME'] . ', ' . $emp_row['FIRST_NAME'] . ' ' . $emp_row['MIDDLE_INITIAL']; ?></td>
                        <td><?php echo $emp_row['EMP_TYPE'] ?></td>
                        <td><?php echo $emp_row['CONTACT_NO'] ?></td>
                        <td><?php echo $emp_row['EMAIL'] ?></td>
                        <td><?php echo $emp_row['ADDRESS'] ?></td>
                        <td>actions</td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">
                        <center>No Employee Found.</center>
                    </td>
                </tr>
<?php
            }
        } else {
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
