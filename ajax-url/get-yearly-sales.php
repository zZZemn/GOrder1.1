<?php
include('../database/db.php');
if (isset($_GET['year'])) {
    $year = $_GET['year'];

    $sql = "SELECT
    m.month_number AS month,
    IFNULL(SUM(s.UPDATED_TOTAL), 0) AS total_sales,
    IFNULL(SUM(s.VAT), 0) AS total_vat
    FROM (
        SELECT 1 AS month_number
        UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
        UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
        UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
        UNION SELECT 11 UNION SELECT 12
    ) AS m
    LEFT JOIN sales s ON m.month_number = MONTH(s.DATE) AND YEAR(s.DATE) = '$year'
    GROUP BY m.month_number";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
?>
            <tr>
                <td><?= date("F", mktime(0, 0, 0, $row['month'], 1)) ?></td>
                <td><?= $row['total_vat'] ?></td>
                <td><?= $row['total_sales'] ?></td>
            </tr>
<?php
        }
    }
}
