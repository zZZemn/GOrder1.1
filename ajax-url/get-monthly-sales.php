<?php 
if(isset($_POST['year'])) {
    include('../database/db.php');
    $year = $_POST['year'];

    $sales_sql = "SELECT DATE_FORMAT(DATE, '%M') AS month, SUM(VAT) AS total_vat, SUM(UPDATED_TOTAL) AS total_sales FROM sales WHERE YEAR(DATE) = '$year' AND PAYMENT >= TOTAL GROUP BY MONTH(DATE)";
    $sales_sql_result = $conn->query($sales_sql);
    if($sales_sql_result->num_rows > 0) {
        while($row = $sales_sql_result->fetch_assoc()) {
            $month = $row['month'];
            $total_vat = $row['total_vat'];
            $total_sales = $row['total_sales'];
            echo "<tr>
                    <td>".$month."</td>
                    <td>".$total_vat."</td>
                    <td>".$total_sales."</td>
                  </tr>";
        }
    }
    else {
        echo "<tr>
              <td colspan='3'><center class='no-sales-found text-danger'>No Sales Found For ".$year."</center></td>
              </tr>";
    }
}
