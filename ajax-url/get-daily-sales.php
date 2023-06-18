<?php 
if(isset($_POST['date'])) {
    include('../database/db.php');
    $date = $_POST['date'];

    $sales_sql = "SELECT * FROM sales WHERE DATE = '$date' AND PAYMENT >= TOTAL ORDER BY TIME DESC";
    $sales_sql_result = $conn->query($sales_sql);
    if($sales_sql_result->num_rows > 0) {
        while($row = $sales_sql_result->fetch_assoc()) {
            echo "<tr>
            <td>".$row['TRANSACTION_ID']."</td>
            <td>".$row['TRANSACTION_TYPE']."</td>
            <td>".$row['CUST_TYPE']."</td>
            <td>".$row['TIME']."</td>
            <td>".$row['SUBTOTAL']."</td>
            <td>".$row['VAT']."</td>
            <td>".$row['DISCOUNT']."</td>
            <td>".$row['TOTAL']."</td>
            <td>".$row['PAYMENT']."</td>
            <td>".$row['CHANGE']."</td>
            <td>".$row['UPDATED_TOTAL']."</td>
            <td>".$row['EMP_ID']."</td>
        </tr>";
        }
    }
    else {
        echo "<tr>
              <td colspan='12'><center class='no-sales-found text-danger'>No Sales Found For ".$date."</center></td>
              </tr>";
    }
}
