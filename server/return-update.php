<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $date = $_GET['id'];
            $return_sql = "SELECT * FROM `return` WHERE RETURN_DATE = '$date'";
            $return_result = $conn->query($return_sql);
            if($return_result->num_rows > 0){
                while($row = $return_result->fetch_assoc()){
                    ?>
                        <tr>
                            <td><?php echo $row['RETURN_ID'] ?></td>
                            <td><?php echo $row['TRANSACTION_ID'] ?></td>
                            <td><?php echo $row['RETURN_DATE'] ?></td>
                            <td><?php echo $row['RETURN_AMOUNT'] ?></td>
                            <td><?php echo $row['RETURN_REASON'] ?></td>
                            <td><a href="../sales/sales-return.php?id=<?php echo $row['TRANSACTION_ID'] ?>" target="_blank" class="btn btn-primary link-to-return"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php
                }
            } else {
                ?>
                    <td colspan="6" class="no-trans text-danger">No Return Transaction on <?php echo $date ?></td>
                <?php
            }
        }
    }
}
