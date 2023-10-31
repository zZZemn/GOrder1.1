<?php
if (isset($_GET['logType'])) {
    include('../database/db.php');
    $logType = $_GET['logType'];
    if ($logType == 'all') {
        $sql = "SELECT cl.*, cu.* FROM `cust_log` AS cl JOIN `customer_user` AS cu ON cl.CUST_ID = cu.CUST_ID";
    } else {
        $sql = "SELECT cl.*, cu.* FROM `cust_log` AS cl JOIN `customer_user` AS cu ON cl.CUST_ID = cu.CUST_ID WHERE cl.LOG_TYPE LIKE '%$logType%'";
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
        ?>
        <tr>
            <td colspan="4">
                <center class="text-danger">No Data Found</center>
            </td>
        </tr>
<?php
    }
}
