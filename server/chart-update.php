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
        $year = date('Y');

        // Initialize sales data array with zero values for each month
        $salesData = array_fill(0, 12, 0);

        $sql = "SELECT (MONTH(`DATE`) - 1) AS month, SUM(TOTAL) AS total_sales 
                FROM sales
                WHERE YEAR(`DATE`) = $year 
                GROUP BY MONTH(`DATE`)";

        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $month = $row['month'];
            $sales = $row['total_sales'];
            $salesData[$month] =  intval($sales);
        }

        echo json_encode($salesData);
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
