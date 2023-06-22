<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['value'])) {
        $value = $_POST['value'];

        if ($value === 'today') {
            $sales_sql = "SELECT * FROM sales WHERE DATE = '$currentDate' ORDER BY TIME DESC";
            $sales_result = $conn->query($sales_sql);
            if ($sales_result->num_rows > 0) {
                while ($row = $sales_result->fetch_assoc()) {
                    $saleTime = date("h:i A", strtotime($row['TIME']));
                    $emp_id = $row['EMP_ID'];
                    $emp_sql = "SELECT FIRST_NAME, LAST_NAME FROM employee WHERE EMP_ID = '$emp_id'";
                    $emp_result = $conn->query($emp_sql);
                    $process_by = '';
                    if($emp_result->num_rows > 0){
                        $process_emp = $emp_result->fetch_assoc();
                        $process_by = $process_emp['FIRST_NAME'].' '.$process_emp['LAST_NAME'];
                    }
                    $sales = "
                            <tr>
                                <td>" . $row['TRANSACTION_ID'] . "</td>
                                <td>" . $row['TRANSACTION_TYPE'] . "</td>
                                <td>" . $saleTime . "</td>
                                <td>" . $row['TOTAL'] . "</td>
                                <td>" . $process_by . "</td>
                                <td class='action-td'>
                                    <a href='sales-return.php?id=" . $row['TRANSACTION_ID'] . "' class='btn btn-dark' target='_blank'>Return <i class='fa-solid fa-rotate-left'></i></a>
                                </td>
                            </tr>";
                    echo $sales;
                }
            } else {
                $sales = "
                        <tr>
                            <td colspan='13'>
                                <center>Empty Sales for $currentDate</center>
                            </td>
                        </tr>";
                echo $sales;
            }
        } elseif ($value === 'this-week') {
            include('../time-date.php');
            $sales_sql = "SELECT * FROM sales WHERE DATE >= '$sevenDaysAgo' AND DATE <= '$currentDate' ORDER BY DATE DESC, TIME DESC";
            $sales_result = $conn->query($sales_sql);
            if ($sales_result->num_rows > 0) {
                while ($row = $sales_result->fetch_assoc()) {
                    $emp_id = $row['EMP_ID'];
                    $emp_sql = "SELECT FIRST_NAME, LAST_NAME FROM employee WHERE EMP_ID = '$emp_id'";
                    $emp_result = $conn->query($emp_sql);
                    $process_by = '';
                    if($emp_result->num_rows > 0){
                        $process_emp = $emp_result->fetch_assoc();
                        $process_by = $process_emp['FIRST_NAME'].' '.$process_emp['LAST_NAME'];
                    }
                    $sales = "
                            <tr>
                                <td>" . $row['TRANSACTION_ID'] . "</td>
                                <td>" . $row['TRANSACTION_TYPE'] . "</td>
                                <td>" . $row['DATE'] . "</td>
                                <td>" . $row['TOTAL'] . "</td>
                                <td>" . $process_by . "</td>
                                <td class='action-td'>
                                    <a href='sales-return.php?id=" . $row['TRANSACTION_ID'] . "' class='btn btn-dark' target='_blank'>Return <i class='fa-solid fa-rotate-left'></i></a>
                                </td>
                            </tr>";
                    echo $sales;
                }
            } else {
                $sales = "
                        <tr>
                            <td colspan='13'>
                                <center>No sales transaction 7 days ago until today.</center>
                            </td>
                        </tr>";
                echo $sales;
            }
        } else {
        }
    } else {
    }
} else {
}
