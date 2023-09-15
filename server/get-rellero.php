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
        if (isset($_GET['type']) && isset($_GET['date'])) {
            $type = $_GET['type'];
            $date = $_GET['date'];

            if ($type === 'all') {
                $get_sql = "SELECT * FROM `rellero` WHERE DATE(`DATE_TIME`) = '$date'";
            } else {
                $get_sql = "SELECT * FROM `rellero` WHERE DATE(`DATE_TIME`) = '$date' AND `TYPE` = '$type'";
            }

            $get_result = $conn->query($get_sql);
            if ($get_result->num_rows > 0) {
                $row = 0;
                while ($get_row = $get_result->fetch_assoc()) {
                    $row++;
                    $one_thousand = $get_row['ONE_THOUSAND'] * 1000;
                    $five_hundred = $get_row['FIVE_HUNDRED'] * 500;
                    $two_hundred = $get_row['TWO_HUNDRED'] * 200;
                    $one_hundred = $get_row['ONE_HUNDRED'] * 100;
                    $fifty = $get_row['FIFTY'] * 50;
                    $twenty = $get_row['TWENTY'] * 20;
                    $ten = $get_row['TEN'] * 10;
                    $five = $get_row['FIVE'] * 5;
                    $one = $get_row['ONE'] * 1;
                    $twenty_five_cents = $get_row['TWENTY_FIVE_CENTS'] * 0.25;

                    $total = $one_thousand +
                        $five_hundred +
                        $two_hundred +
                        $one_hundred +
                        $fifty +
                        $twenty +
                        $ten +
                        $five +
                        $one +
                        $twenty_five_cents;
?>
                    <tr>
                        <td><?= $row ?></td>
                        <td><?php
                            $date_time = date("g:i A, F j, Y", strtotime($get_row['DATE_TIME']));
                            echo $date_time;
                            ?>
                        </td>
                        <td>
                            <?php
                            $emp_id = $get_row['EMP_ID'];
                            $emp_sql = "SELECT * FROM `employee` WHERE `EMP_ID` = '$emp_id'";
                            $emp_result = $conn->query($emp_sql);
                            if ($emp_result->num_rows > 0) {
                                $emp_row = $emp_result->fetch_assoc();
                                echo $emp_row['FIRST_NAME'].' '.$emp_row['MIDDLE_INITIAL'].' '.$emp_row['LAST_NAME'];
                            } else {
                                echo '';
                            }
                            ?>
                        </td>
                        <td><?= $get_row['ONE_THOUSAND'] ?></td>
                        <td><?= $get_row['FIVE_HUNDRED'] ?></td>
                        <td><?= $get_row['TWO_HUNDRED'] ?></td>
                        <td><?= $get_row['ONE_HUNDRED'] ?></td>
                        <td><?= $get_row['FIFTY'] ?></td>
                        <td><?= $get_row['TWENTY'] ?></td>
                        <td><?= $get_row['TEN'] ?></td>
                        <td><?= $get_row['FIVE'] ?></td>
                        <td><?= $get_row['ONE'] ?></td>
                        <td><?= $get_row['TWENTY_FIVE_CENTS'] ?></td>
                        <td class="total-td"><?= $total ?></td>
                        <td><?= $get_row['TYPE'] ?></td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="15">
                        <center class="no-sales-found">No Cash found.</center>
                    </td>
                </tr>
<?php
            }
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
