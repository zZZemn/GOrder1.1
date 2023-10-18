<?php
function accessDenied()
{
    echo <<<HTML
        <head>
            <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
            <title>Access Denied!</title>
            <link rel='stylesheet' href='css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
        HTML;
}

session_start();
if (isset($_SESSION['id'])) {
    include('database/db.php');
    include('time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['rpt_type'])) {
?>
            <html>

            <head>
                <title>Print Report</title>
                <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                <link rel="stylesheet" href="css/access-denied.css">
                <link rel="stylesheet" href="css/print-report.css">
            </head>

            <body>
                <div class="">
                    <?php

                    // Daily sales
                    if ($_GET['rpt_type'] === 'DailySales') {
                        if (isset($_GET['date'])) {
                            $salesDate = $_GET['date'];
                            $transactionType = false;
                            $customerType = false;
                            $processBy = false;

                            $sql = "SELECT s.*, e.* FROM `sales` s JOIN `employee` e ON s.EMP_ID = e.EMP_ID WHERE s.DATE = '$salesDate'";

                            if (isset($_GET['transaction_type']) && $_GET['transaction_type'] !== 'all') {
                                $transactionType = true;
                                $transactionTypeValue = $_GET['transaction_type'];
                                $sql .= " AND s.TRANSACTION_TYPE = '$transactionTypeValue'";
                            }

                            if (isset($_GET['cust_type']) && $_GET['cust_type'] !== 'all') {
                                $customerType = true;
                                $customerTypeValue = $_GET['cust_type'];
                                $sql .= " AND s.CUST_TYPE = '$customerTypeValue'";
                            }

                            if (isset($_GET['process_by']) && $_GET['process_by'] !== 'all') {
                                $processBy = true;
                                $processByValue = $_GET['process_by'];
                                $sql .= " AND s.EMP_ID = '$processByValue'";
                            }
                    ?>
                            <table class="table">
                                <tr>
                                    <th colspan="12">
                                        <center><img class="logo" src="img/ggd-logo.png"></center>
                                        <center>Golden Gate Drugstore</center>
                                        <center>Patubig, Marilao, Bulacan</center>
                                        <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                        <center>Printed on <?= $currentDate ?></center>
                                        <center class="m-2">
                                            <h5>Daily Sales</h5>
                                        </center>
                                        <div class="filter-container">
                                            Filters:
                                            <br>
                                            <span>Sales Date: <?= $salesDate ?></span>
                                            <br>
                                            <span>Type: <?= ($transactionType) ? $_GET['transaction_type'] : 'All' ?></span>
                                            <br>
                                            <span>Type: <?= ($customerType) ? $_GET['cust_type'] : 'All' ?></span>
                                            <br>
                                            <span>Process By: <?= ($processBy) ? $_GET['process_by'] : 'All' ?></span>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Transaction Type</th>
                                    <th>Customer Type</th>
                                    <th>Time</th>
                                    <th>Subtotal</th>
                                    <th>VAT</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Change</th>
                                    <th>Updated Total</th>
                                    <th>Process By</th>
                                </tr>
                                <?php

                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $emp_name = $row['FIRST_NAME'] . ' ' . $row['MIDDLE_INITIAL'] . ' ' . $row['LAST_NAME'];
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
                                ?>
                                    <tr>
                                        <td colspan="12">
                                            <center>End</center>
                                        </td>
                                    </tr>
                                <?php
                                } else {
                                    echo "<tr>
                                            <td colspan='12'><center>No sales found.</center></td>
                                          </tr>";
                                }
                                ?>
                            </table>
                        <?php
                        } else {
                            accessDenied();
                        }
                    } // End of Daily Sales
                    elseif ($_GET['rpt_type'] === 'MonthlySales') {
                        if (isset($_GET['year'])) {
                            $year = $_GET['year'];
                            $sales_sql = "SELECT DATE_FORMAT(DATE, '%M') AS month, SUM(VAT) AS total_vat, SUM(UPDATED_TOTAL) AS total_sales FROM sales WHERE YEAR(DATE) = '$year' AND PAYMENT >= TOTAL GROUP BY MONTH(DATE)";

                        ?>
                            <table class="table">
                                <tr>
                                    <th colspan="3">
                                        <center><img class="logo" src="img/ggd-logo.png"></center>
                                        <center>Golden Gate Drugstore</center>
                                        <center>Patubig, Marilao, Bulacan</center>
                                        <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                        <center>Printed on <?= $currentDate ?></center>
                                        <center class="m-2">
                                            <h5>Monthly Sales</h5>
                                        </center>
                                        <div class="filter-container">
                                            Filter:
                                            <br>
                                            <span>Year: <?= $year ?></span>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Vat</th>
                                    <th>Total Sales</th>
                                </tr>
                            <?php
                            $salesResult = $conn->query($sales_sql);
                            if ($salesResult->num_rows > 0) {
                                while ($salesRow = $salesResult->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . $salesRow['month'] . "</td>
                                        <td>" . $salesRow['total_vat'] . "</td>
                                        <td>" . $salesRow['total_sales'] . "</td>
                                      </tr>";
                                }
                                echo '<tr><td colspan="3"><center>End</center></td></tr>';
                            } else {
                                echo "<tr>
                                        <td colspan='3'><center>No data found.</center></td>
                                      </tr>";
                            }
                        } else {
                            accessDenied();
                        }
                    } //End of Monthly Sales

                    // yearly sales
                    elseif ($_GET['rpt_type'] === 'YearlySales') {
                        $year_sql = "SELECT YEAR(DATE) AS year, SUM(UPDATED_TOTAL) AS total_sales, SUM(VAT) AS total_vat FROM sales WHERE PAYMENT >= TOTAL GROUP BY YEAR(DATE)";
                        $year_result = $conn->query($year_sql);
                            ?>
                            <table class="table">
                                <tr>
                                    <th colspan="3">
                                        <center><img class="logo" src="img/ggd-logo.png"></center>
                                        <center>Golden Gate Drugstore</center>
                                        <center>Patubig, Marilao, Bulacan</center>
                                        <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                        <center>Printed on <?= $currentDate ?></center>
                                        <center class="m-2">
                                            <h5>Yearly Sales</h5>
                                        </center>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Year</th>
                                    <th>Total Vat</th>
                                    <th>Total Sales</th>
                                </tr>
                                <?php
                                if ($year_result->num_rows > 0) {
                                    while ($row = $year_result->fetch_assoc()) {
                                        echo
                                        "<tr>
                                            <td>" . $row['year'] . "</td>
                                            <td>" . $row['total_vat'] . "</td>
                                            <td>" . $row['total_sales'] . "</td>
                                        </tr>";
                                    }
                                    echo '<tr><td colspan="3"><center>End</center></td></tr>';
                                } else {
                                    echo "<tr>
                                <td colspan='3'><center>No data found.</center></td>
                              </tr>";
                                }
                                ?>
                            </table>
                            <?php
                        } //End of Yearly Sales
                        // Return
                        elseif ($_GET['rpt_type'] === 'Return') {
                            if (isset($_GET['date'])) {
                                $date = $_GET['date'];
                                $return_sql = "SELECT * FROM `return` WHERE RETURN_DATE = '$date'";
                                $return_result = $conn->query($return_sql);
                            ?>
                                <table class="table">
                                    <tr>
                                        <th colspan="4">
                                            <center><img class="logo" src="img/ggd-logo.png"></center>
                                            <center>Golden Gate Drugstore</center>
                                            <center>Patubig, Marilao, Bulacan</center>
                                            <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                            <center>Printed on <?= $currentDate ?></center>
                                            <center class="m-2">
                                                <h5>Return</h5>
                                            </center>
                                            <div class="filter-container">
                                                Filter:
                                                <br>
                                                <span>Date: <?= $date ?></span>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>ID</th>
                                        <th>Transaction ID</th>
                                        <th>Return Amount</th>
                                        <th>Return Reason</th>
                                    </tr>
                                    <?php
                                    if ($return_result->num_rows > 0) {
                                        while ($returnRow = $return_result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?php echo $returnRow['RETURN_ID'] ?></td>
                                                <td><?php echo $returnRow['TRANSACTION_ID'] ?></td>
                                                <td><?php echo $returnRow['RETURN_AMOUNT'] ?></td>
                                                <td><?php echo $returnRow['RETURN_REASON'] ?></td>
                                            </tr>
                                    <?php
                                        }
                                        echo '<tr><td colspan="4"><center>End</center></td></tr>';
                                    } else {
                                        echo "<tr>
                                                <td colspan='4'><center>No data found.</center></td>
                                              </tr>";
                                    }
                                    ?>
                                </table>
                                <?php
                            } else {
                                accessDenied();
                            }
                        } //End of Return
                        elseif ($_GET['rpt_type'] === 'CashRegistered') {
                            if (isset($_GET['date'])) {
                                $date = $_GET['date'];
                                if (isset($_GET['process_type'])) {
                                    if ($_GET['process_type'] === 'all') {
                                        $sql = "SELECT r.*, e.* FROM rellero r JOIN employee e ON r.EMP_ID = e.EMP_ID WHERE DATE(r.DATE_TIME) = '$date'";
                                    } else {
                                        $type = $_GET['process_type'];
                                        $sql = "SELECT r.*, e.* FROM rellero r JOIN employee e ON r.EMP_ID = e.EMP_ID WHERE DATE(r.DATE_TIME) = '$date' AND r.TYPE = '$type'";
                                    }
                                    $result = $conn->query($sql);
                                ?>
                                    <table class="table">
                                        <tr>
                                            <th colspan="14">
                                                <center><img class="logo" src="img/ggd-logo.png"></center>
                                                <center>Golden Gate Drugstore</center>
                                                <center>Patubig, Marilao, Bulacan</center>
                                                <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                                <center>Printed on <?= $currentDate ?></center>
                                                <center class="m-2">
                                                    <h5>Cash Register Report</h5>
                                                </center>
                                                <div class="filter-container">
                                                    Filter:
                                                    <br>
                                                    <span>Date: <?= $date ?></span>
                                                    <br>
                                                    <span>Type: <?= $_GET['process_type'] ?></span>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Process By</th>
                                            <th>₱ 1000</th>
                                            <th>₱ 500</th>
                                            <th>₱ 200</th>
                                            <th>₱ 100</th>
                                            <th>₱ 50</th>
                                            <th>₱ 20</th>
                                            <th>₱ 10</th>
                                            <th>₱ 5</th>
                                            <th>₱ 1</th>
                                            <th>¢ 25</th>
                                            <th>Total</th>
                                            <th>Type</th>
                                        </tr>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $denominations = [
                                                    'ONE_THOUSAND' => 1000,
                                                    'FIVE_HUNDRED' => 500,
                                                    'TWO_HUNDRED' => 200,
                                                    'ONE_HUNDRED' => 100,
                                                    'FIFTY' => 50,
                                                    'TWENTY' => 20,
                                                    'TEN' => 10,
                                                    'FIVE' => 5,
                                                    'ONE' => 1,
                                                    'TWENTY_FIVE_CENTS' => 0.25
                                                ];

                                                $total = 0;

                                                foreach ($denominations as $key => $value) {
                                                    $total += $row[$key] * $value;
                                                }
                                        ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        $date_time = date("g:i A, F j, Y", strtotime($row['DATE_TIME']));
                                                        echo $date_time;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $emp_id = $row['EMP_ID'];
                                                        $emp_result = $conn->query("SELECT * FROM `employee` WHERE `EMP_ID` = '$emp_id'");
                                                        if ($emp_result->num_rows > 0) {
                                                            $emp_row = $emp_result->fetch_assoc();
                                                            echo $emp_row['FIRST_NAME'] . ' ' . $emp_row['MIDDLE_INITIAL'] . ' ' . $emp_row['LAST_NAME'];
                                                        } else {
                                                            echo '';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $row['ONE_THOUSAND'] ?></td>
                                                    <td><?= $row['FIVE_HUNDRED'] ?></td>
                                                    <td><?= $row['TWO_HUNDRED'] ?></td>
                                                    <td><?= $row['ONE_HUNDRED'] ?></td>
                                                    <td><?= $row['FIFTY'] ?></td>
                                                    <td><?= $row['TWENTY'] ?></td>
                                                    <td><?= $row['TEN'] ?></td>
                                                    <td><?= $row['FIVE'] ?></td>
                                                    <td><?= $row['ONE'] ?></td>
                                                    <td><?= $row['TWENTY_FIVE_CENTS'] ?></td>
                                                    <td class="total-td"><?= $total ?></td>
                                                    <td><?= $row['TYPE'] ?></td>
                                                </tr>
                                        <?php
                                            }
                                            echo '<tr><td colspan="14"><center>End</center></td></tr>';
                                        } else {
                                            echo "<tr>
                                                <td colspan='14'><center>No data found.</center></td>
                                              </tr>";
                                        }
                                        ?>
                                    </table>
                                <?php
                                } else {
                                    accessDenied();
                                }
                            } else {
                                accessDenied();
                            }
                        } elseif ($_GET['rpt_type'] === 'Inventory') {
                            if (isset($_GET['cat'], $_GET['sub_cat'])) {
                                $cat = $_GET['cat'];
                                $sub_cat = $_GET['sub_cat'];

                                $categorySql = "SELECT * FROM `category` WHERE `CAT_ID` = '$cat'";
                                $subCatSql = "SELECT * FROM `sub_category` WHERE `SUB_CAT_ID` = '$sub_cat'";

                                if (($categoryResult = $conn->query($categorySql)) && ($subCatResult = $conn->query($subCatSql))) {
                                    if ($categoryResult->num_rows > 0) {
                                        $categoryRow = $categoryResult->fetch_assoc();
                                        $categoryFinal = $categoryRow['CAT_NAME'];
                                    } else {
                                        $categoryFinal = 'All';
                                    }

                                    if ($subCatResult->num_rows > 0) {
                                        $subCatRow = $subCatResult->fetch_assoc();
                                        $subCatFinal = $subCatRow['SUB_CAT_NAME'];
                                    } else {
                                        $subCatFinal = 'All';
                                    }
                                }


                                if ($cat == 'all' && $sub_cat == '') {
                                    $sql = "SELECT i.*, p.* FROM inventory i JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID";
                                } elseif ($sub_cat == '') {
                                    $sql = "SELECT i.*, p.*, c.* FROM inventory i JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID JOIN sub_category sc ON p.SUB_CAT_ID = sc.SUB_CAT_ID JOIN category c ON sc.CAT_ID = c.CAT_ID WHERE c.CAT_ID = '$cat'";
                                } else {
                                    $sql = "SELECT i.*,p.* FROM inventory i JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID WHERE p.SUB_CAT_ID = '$sub_cat'";
                                }

                                $result = $conn->query($sql);
                                ?>
                                <table class="table">
                                    <tr>
                                        <th colspan="4">
                                            <center><img class="logo" src="img/ggd-logo.png"></center>
                                            <center>Golden Gate Drugstore</center>
                                            <center>Patubig, Marilao, Bulacan</center>
                                            <center>Printed by <?= $emp['FIRST_NAME'] . ' ' . $emp['LAST_NAME'] ?></center>
                                            <center>Printed on <?= $currentDate ?></center>
                                            <center class="m-2">
                                                <h5>Inventory Report</h5>
                                            </center>
                                            <div class="filter-container">
                                                Filter:
                                                <br>
                                                <span>Category: <?= $categoryFinal ?></span>
                                                <br>
                                                <span>Sub Category: <?= $subCatFinal ?></span>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Inventory ID</th>
                                        <th>Product</th>
                                        <th>Expiration Date</th>
                                        <th>Quantity</th>
                                    </tr>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?= $row['INV_ID'] ?></td>
                                                <td>
                                                    <?php
                                                    echo $row['PRODUCT_NAME'];
                                                    echo (isset($row['MG']) && $row['MG'] !== '') ? ' ' . $row['MG'] . 'mg ' : '';
                                                    echo (isset($row['G']) && $row['G'] !== '') ? ' ' . $row['G'] . 'g ' : '';
                                                    echo (isset($row['ML']) && $row['ML'] !== '') ? ' ' . $row['ML'] . 'ml ' : '';

                                                    ?>
                                                </td>
                                                <td><?= $row['EXP_DATE'] ?></td>
                                                <td><?= $row['QUANTITY'] ?></td>
                                            </tr>
                                    <?php
                                        }
                                        echo '<tr><td colspan="14"><center>End</center></td></tr>';
                                    } else {
                                        echo "<tr>
                                                <td colspan='14'><center>No data found.</center></td>
                                              </tr>";
                                    }
                                    ?>
                                </table>
                        <?php
                            } else {
                                accessDenied();
                            }
                        } else {
                            echo 'endddd';
                        }
                        ?>
                </div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="js/print-report.js"></script>
            </body>

            </html>
<?php
        } else {
            accessDenied();
        }
    } else {
        accessDenied();
    }
} else {
    header("Location: index.php");
    exit;
}
