<?php
if (isset($_GET['date']) && isset($_GET['transactionType']) && isset($_GET['custType']) && isset($_GET['processBy'])) {
    include('../database/db.php');
    $date = $_GET['date'];
    $transactionType = $_GET['transactionType'];
    $custType = $_GET['custType'];
    $processBy = $_GET['processBy'];

    if ($transactionType !== 'all') {
        if ($custType !== 'all') {
            if ($processBy !== 'all') {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `TRANSACTION_TYPE` = '$transactionType' AND `CUST_TYPE` = '$custType' AND `EMP_ID` = '$processBy' ORDER BY TIME DESC";
            } else {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `TRANSACTION_TYPE` = '$transactionType' AND `CUST_TYPE` = '$custType' ORDER BY TIME DESC";
            }
        } else {
            if ($processBy !== 'all') {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `TRANSACTION_TYPE` = '$transactionType' AND `EMP_ID` = '$processBy' ORDER BY TIME DESC";
            } else {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `TRANSACTION_TYPE` = '$transactionType' ORDER BY TIME DESC";
            }
        }
    } else {
        if ($custType !== 'all') {
            if ($processBy !== 'all') {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `CUST_TYPE` = '$custType' AND `EMP_ID` = '$processBy' ORDER BY TIME DESC";
            } else {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `CUST_TYPE` = '$custType' ORDER BY TIME DESC";
            }
        } else {
            if ($processBy !== 'all') {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL AND `EMP_ID` = '$processBy' ORDER BY TIME DESC";
            } else {
                $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL ORDER BY TIME DESC";
            }
        }
    }

    // echo '<center>' . $sales_sql . '</center>';
    $sales_sql_result = $conn->query($sales_sql);
    if ($sales_sql_result->num_rows > 0) {
        while ($row = $sales_sql_result->fetch_assoc()) {
            $emp_id = $row['EMP_ID'];
            $check_emp_sql = "SELECT `FIRST_NAME`, `LAST_NAME` FROM employee WHERE `EMP_ID` = '$emp_id'";
            $check_emp_result = $conn->query($check_emp_sql);
            if ($check_emp_result->num_rows > 0) {
                $emp = $check_emp_result->fetch_assoc();

                $emp_name = $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'];
            } else {
                $emp_name = '';
            }

            echo "<tr>
            <td>" . $row['TRANSACTION_ID'] . "</td>
            <td>" . $row['TRANSACTION_TYPE'] . "</td>
            <td>" . $row['CUST_TYPE'] . "</td>
            <td>" . date("h:i A", strtotime($row['TIME'])) . "</td>
            <td>" . $row['SUBTOTAL'] . "</td>
            <td>" . $row['VAT'] . "</td>
            <td>" . $row['DISCOUNT'] . "</td>
            <td>" . $row['TOTAL'] . "</td>
            <td>" . $row['PAYMENT'] . "</td>
            <td>" . $row['CHANGE'] . "</td>
            <td>" . $row['UPDATED_TOTAL'] . "</td>
            <td>" . $emp_name . "</td>
        </tr>";
        }
    } else {
        echo "<tr>
              <td colspan='12'><center class='no-sales-found text-danger'>No Sales Found For " . $date . "</center></td>
              </tr>";
    }
}
