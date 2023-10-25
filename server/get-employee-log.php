<?php
include('../database/db.php');

if (isset($_GET['emp_id'], $_GET['log_type'])) {
    $empId = $_GET['emp_id'];
    $logType = $_GET['log_type'];

    $sql = "SELECT el.*, e.* FROM `emp_log` el JOIN `employee` e ON el.EMP_ID = e.EMP_ID";

    if ($empId != 'all' && $logType != 'all') {
        $sql .= " WHERE el.EMP_ID = '$empId' AND el.LOG_TYPE LIKE '%$logType%'";
    } elseif ($empId != 'all') {
        $sql .= " WHERE el.EMP_ID = '$empId'";
    } elseif ($logType != 'all') {
        $sql .= " WHERE el.LOG_TYPE LIKE '%$logType%'";
    }

    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
?>
                <tr>
                    <td><?= $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'] ?></td>
                    <td><?= $row['LOG_TYPE'] ?></td>
                    <td><?= $row['LOG_DATE'] ?></td>
                    <td><?= date('h:i A', strtotime($row['LOG_TIME'])) ?></td>
                </tr>
            <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">
                    <center class="text-danger">No Data Found</center>
                </td>
            </tr>
<?php
        }
    } else {
        echo '404';
    }
} else {
    echo '404';
}
