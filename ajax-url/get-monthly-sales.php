<?php
$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];


if (isset($_GET['year'], $_GET['month'], $_GET['transactionType'], $_GET['custType'], $_GET['processBy'])) {
    include('../database/db.php');
    $year = $_GET['year'];
    $month = $_GET['month'];
    $transactionType = $_GET['transactionType'];
    $custType = $_GET['custType'];
    $processBy = $_GET['processBy'];

    if ($transactionType !== 'all') {
        if ($custType !== 'all') {
            if ($processBy !== 'all') {
                // user transaction type, custtype and process by
                $sql = "SELECT * FROM `sales` WHERE `EMP_ID` = '$processBy' AND `CUST_TYPE` = '$custType' AND `TRANSACTION_TYPE` = '$transactionType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            } else {
                // user transaction type, custtype
                $sql = "SELECT * FROM `sales` WHERE `CUST_TYPE` = '$custType' AND `TRANSACTION_TYPE` = '$transactionType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            }
        } else {
            if ($processBy !== 'all') {
                // user transaction type, process by
                $sql = "SELECT * FROM `sales` WHERE `EMP_ID` = '$processBy' AND `TRANSACTION_TYPE` = '$transactionType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            } else {
                // user transaction type
                $sql = "SELECT * FROM `sales` WHERE `TRANSACTION_TYPE` = '$transactionType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            }
        }
    } else {
        if ($custType !== 'all') {
            if ($processBy !== 'all') {
                // user  custtype and process by
                $sql = "SELECT * FROM `sales` WHERE `EMP_ID` = '$processBy' AND `CUST_TYPE` = '$custType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            } else {
                // user  custtype
                $sql = "SELECT * FROM `sales` WHERE `CUST_TYPE` = '$custType' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            }
        } else {
            if ($processBy !== 'all') {
                // user  process by
                $sql = "SELECT * FROM `sales` WHERE `EMP_ID` = '$processBy' AND YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            } else {
                // all
                $sql = "SELECT * FROM `sales` WHERE YEAR(`DATE`) = '$year' AND MONTH(`DATE`) = '$month' ORDER BY `DATE` ASC, `TIME` ASC";
            }
        }
    }

    $salesResult = $conn->query($sql);
    if ($salesResult->num_rows > 0) {
        while ($row = $salesResult->fetch_assoc()) {
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
            <td>" . date("F j Y", strtotime($row['DATE'])) . "</td>
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
              <td colspan='13'><center class='no-sales-found text-danger'>No Sales Found For " . $months[$month] . ', ' . $year . "</center></td>
              </tr>";
    }
} else {
    echo 'Gago';
}
