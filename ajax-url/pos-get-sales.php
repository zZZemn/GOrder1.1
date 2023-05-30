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
                    $sales = "
                            <tr>
                                <td><a href='view-invoice.php?id=".$row['TRANSACTION_ID']."' target='_blank'>" . $row['TRANSACTION_ID'] . "</a></td>
                                <td>" . $row['TRANSACTION_TYPE'] . "</td>
                                <td>" . $row['CUST_TYPE'] . "</td>
                                <td>" . $saleTime . "</td>
                                <td>" . $row['SUBTOTAL'] . "</td>
                                <td>" . $row['VAT'] . "</td>
                                <td>" . $row['DISCOUNT'] . "</td>
                                <td>" . $row['TOTAL'] . "</td>
                                <td>" . $row['PAYMENT'] . "</td>
                                <td>" . $row['CHANGE'] . "</td>
                                <td>" . $row['EMP_ID'] . "</td>
                            </tr>";
                    echo $sales;
                }
            } else {
                $sales = "
                        <tr>
                            <td colspan='11'>
                                <center>Empty Sales for $currentDate</center>
                            </td>
                        </tr>";
                        echo $sales;
            }
        } elseif ($value === 'this-week') {
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $currentDate = date('Y-m-d');
            $sales_sql = "SELECT * FROM sales WHERE DATE >= '$sevenDaysAgo' AND DATE <= '$currentDate' ORDER BY DATE DESC, TIME DESC";
            $sales_result = $conn->query($sales_sql);
            if ($sales_result->num_rows > 0) {
                while ($row = $sales_result->fetch_assoc()) {
                    $saleTime = date("h:i A", strtotime($row['TIME']));
                    $sales = "
                            <tr>
                                <td><a href='view-invoice.php?id=".$row['TRANSACTION_ID']."' target='_blank'>" . $row['TRANSACTION_ID'] . "</a></td>
                                <td>" . $row['TRANSACTION_TYPE'] . "</td>
                                <td>" . $row['CUST_TYPE'] . "</td>
                                <td>" . $saleTime . "</td>
                                <td>" . $row['SUBTOTAL'] . "</td>
                                <td>" . $row['VAT'] . "</td>
                                <td>" . $row['DISCOUNT'] . "</td>
                                <td>" . $row['TOTAL'] . "</td>
                                <td>" . $row['PAYMENT'] . "</td>
                                <td>" . $row['CHANGE'] . "</td>
                                <td>" . $row['EMP_ID'] . "</td>
                            </tr>";
                            echo $sales;
                }
            } else {
                $sales = "
                        <tr>
                            <td colspan='11'>
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
