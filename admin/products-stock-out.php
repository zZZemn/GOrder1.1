<?php
error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/access-denied.css">
    <link rel="stylesheet" href="../css/message.css">
    <link rel="stylesheet" href="../css/loading.css">
    <link rel="stylesheet" href="../css/products-stock-out.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Stock Out</title>
</head>

<body>
    <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active") : ?>
        <nav class="top-nav bg-dark">

            <i class="fa-solid fa-bars menu"></i>

            <img class="logo" src="../img/ggd-text-logo.png" alt="Golden Gate Drugstore">

            <ul>
                <li class="message-dropdown dropdown">
                    <a>
                        <i class="fa-solid fa-message"></i>
                    </a>
                </li>
                <div class="message-dropdown-container">
                    <?php
                    $messages = "SELECT * FROM messages ORDER BY LATEST_MESS_TIMESTAMP DESC";
                    $messages_result = $conn->query($messages);
                    if ($messages_result->num_rows > 0) {
                    ?>
                        <?php

                        while ($messages_row = $messages_result->fetch_assoc()) {
                            $mess_id = $messages_row['MESS_ID'];
                            $message = "SELECT * FROM message WHERE MESS_ID = $mess_id";
                            $message_result = $conn->query($message);

                            if ($message_result->num_rows > 0) {
                                $latest_message = "SELECT * FROM message WHERE MESS_ID = $mess_id AND TIMESTAMP = ( SELECT MAX(TIMESTAMP) FROM message WHERE MESS_ID = $mess_id)";
                                $latest_message_result = $conn->query($latest_message);
                                $latest_message_row = $latest_message_result->fetch_assoc();

                                $message_id = $latest_message_row['MESS_ID'];

                                $customer = "SELECT * FROM customer_user WHERE CUST_ID = $message_id";
                                $customer_result = $conn->query($customer);
                                $customer_row = $customer_result->fetch_assoc();

                        ?>
                                <a href="#" class="dropdown-message <?php echo $customer_row['CUST_ID'] ?>">
                                    <div class="from">
                                        <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>">
                                        <h3><?php echo $customer_row['FIRST_NAME'] . " " . $customer_row['LAST_NAME']; ?></h3>
                                    </div>
                                    <p><?php echo $latest_message_row['MESSAGE_BODY'] ?></p>
                                    <article><?php echo $latest_message_row['TIMESTAMP'] ?></article>
                                </a>
                                <hr>
                        <?php
                            }
                        }
                    } else {

                        ?>
                        <center class="text-light">No message found</center>
                    <?php
                    }
                    ?>
                </div>

                <li class="notification-dropdown dropdown">
                    <i class="fa-solid fa-bell"></i>
                    <div id="notifications-count">

                    </div>

                    <?php
                    ?>
                </li>
                <div class="notification-dropdown-container" id="notification-dropdown-container">

                </div>

                <li class="avatar-dropdown dropdown">
                    <em class="admin-em"><?php echo $emp['EMP_TYPE'] ?></em>
                    <img src="../img/userprofile/<?php echo $emp['PICTURE'] ?>">
                </li>
                <div class="avatar-dropdown-container">
                    <a href="avatar-profile.php"><i class="fa-solid fa-user"></i>Profile</a>
                    <hr>
                    <a href="../process/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
                </div>
            </ul>


        </nav>

        <!-- side nav -->

        <div class="sidenav">

            <button class="dropdown-btn mobile-only">
                <i class="fa-solid fa-users"></i><?php echo $emp['FIRST_NAME'] . " " . $emp['LAST_NAME'] ?>
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="avatar-profile.php"><i class="fa-solid fa-user-tie"></i>Profile</a>
                <a href="messages.php"><i class="fa-solid fa-message"></i>Messages</a>
                <a href="avatar-settings.php"><i class="fa-solid fa-gear"></i>Settings</a>
                <a href="../process/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Log Out</a>
            </div>

            <hr class="mobile-only">

            <a href="notification.php" class="mobile-only"><i class="fa-solid fa-bell"></i>Notification</a>

            <hr class="mobile-only">

            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>

            <hr>

            <a href="../sales/pos.php"><i class="fa-solid fa-calculator"></i>POS</a>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-capsules"></i>Products<i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="products-allproducts.php"><i class="fa-solid fa-prescription"></i>All Products</a>
                <a href="products-inventory.php"><i class="fa-solid fa-boxes-stacked"></i>Inventory</a>
                <a href="products-deliver.php"><i class="fa-solid fa-truck"></i>Deliver</a>
                <a href="products-supplier.php"><i class="fa-solid fa-building"></i>Supplier</a>
                <a href="products-stock-out.php" class="nav-active"><i class="fa-solid fa-layer-group"></i>Stock Out</a>
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-folder"></i>Reports
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="reports-daily-sales.php"><i class="fa-solid fa-chart-column"></i>Daily Sales</a>
                <a href="reports-monthly-sales.php"><i class="fa-solid fa-chart-column"></i>Monthly Sales</a>
                <a href="reports-yearly-sales.php"><i class="fa-solid fa-chart-column"></i>Yearly Sales</a>
                <a href="products-return.php"><i class="fa-solid fa-rotate-left"></i>Returns</a>
                <a href="rellero.php"><i class="fa-solid fa-money-bill"></i>Cash Register</a>
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-users"></i>Users
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="users-employee.php"><i class="fa-solid fa-user-tie"></i>Employee</a>
                <a href="users-customer.php"><i class="fa-solid fa-people-group"></i>Customer</a>
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-wrench"></i>Maintenance
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="maintenance-tax.php"><i class="fa-solid fa-percent"></i>Tax</a>
                <a href="maintenance-discount.php"><i class="fa-solid fa-percent"></i>Discount</a>
                <a href="maintenance-category.php"><i class="fa-solid fa-list"></i>Category</a>
                <a href="maintenance-address.php"><i class="fa-solid fa-location-dot"></i>Address</a>
                <a href="maintenance-branch.php"><i class="fa-solid fa-code-branch"></i>Branch</a>
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-user-pen"></i>Logs
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="logs-employee.php"><i class="fa-solid fa-user-tie"></i>Employee</a>
                <!-- <a href="logs-customer.php"><i class="fa-solid fa-people-group"></i>Customer</a> -->
            </div>

            <hr>

            <a href="../backup/backup.php" target="_blank"><i class="fa-solid fa-database"></i>Backup</a>
        </div>


        <div class="main">
            <div class="alert alert-success bg-success"></div>
            <div class="alert alert-danger bg-danger"></div>

            <div class="stock-out-filtering-container">
                <div class="search-container" id="search-form">
                    <input type="text" class="form-control" name="search" id="search-input" placeholder="Search Stock Out ID...">
                    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>

                <div class="select-stockout-only">
                    <div class="contents-input-container">
                        <select class="stockout-select-branch form-control" name="branch_select" id="branch_select">
                            <option value="all">All</option>
                            <?php
                            $branch_sql = $conn->query("SELECT * FROM `branch` WHERE `STATUS` = 'Active'");
                            if ($branch_sql->num_rows > 0) {
                                while ($branch = $branch_sql->fetch_assoc()) {
                                    echo '<option value="' . $branch['ID'] . '">' . $branch['BRANCH'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <label class="product-add-label">Select Branch</label>
                    </div>

                    <div class="contents-input-container">
                        <select class="stockout-select-emp form-control" name="emp_select" id="emp_select">
                            <option value="all">All</option>
                            <?php
                            $emp_sql = $conn->query("SELECT * FROM `employee` WHERE `EMP_STATUS` = 'active'");
                            if ($emp_sql->num_rows > 0) {
                                while ($emp_row = $emp_sql->fetch_assoc()) {
                                    echo '<option value="' . $emp_row['EMP_ID'] . '">' . $emp_row['FIRST_NAME'] . ' ' . $emp_row['MIDDLE_INITIAL'] . ' ' . $emp_row['LAST_NAME'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <label class="product-add-label">Process By</label>
                    </div>
                </div>
            </div>


            <div class="stock-out-list-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Stock Out ID</th>
                            <th>Branch</th>
                            <th>Process by</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="stock-out-container">

                    </tbody>
                </table>
            </div>

            <div class="print-add-btn-container">
                <button id="addStockOutOpen" class="btnAddStockOut btn btn-primary" type="button"><i class="fa-solid fa-plus"></i>New Stock Out</button>
                <button type="button" id="printReport" class="btn btn-primary">Print Report</button>
            </div>

            <form class="add-stock-out" id="frm-add-stock-out">
                <center>Add Stock Out</center>
                <div class="contents-input-container">
                    <select class="stockout-select-branch form-control" name="add_branch_select" id="add_branch_select" required>
                        <option disabled selected>Select Branch</option>
                        <?php
                        $branch_sql = $conn->query("SELECT * FROM `branch` WHERE `STATUS` = 'Active'");
                        if ($branch_sql->num_rows > 0) {
                            while ($branch = $branch_sql->fetch_assoc()) {
                                echo '<option value="' . $branch['ID'] . '">' . $branch['BRANCH'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Please Select Branch.
                    </div>
                    <label class="product-add-label">Select Branch</label>
                </div>
                <div class="contents-input-container">
                    <input type="date" id="add_date" class="form-control" name="add_date" required max="<?php echo date('Y-m-d'); ?>">
                    <label class="product-add-label">Delivery Date</label>
                    <div class="invalid-feedback">
                        Advance date are not allowed.
                    </div>
                </div>
                <center class="add-bnts">
                    <button type="reset" id="so-add-cancel" class="btn btn-dark">Cancel</button>
                    <button type="submit" id="so-add-save" class="btn btn-primary">Save</button>
                </center>
            </form>


            <!-- edit form -->
            <form class="add-stock-out" id="frm-edit-stock-out">
                <center>Edit <span id="edit-stock-out-id"></span></center>
                <div class="contents-input-container">
                    <select class="stockout-select-branch form-control" name="edit_branch_select" id="edit_branch_select" required>
                        <option disabled selected>Select Branch</option>
                        <?php
                        $branch_sql = $conn->query("SELECT * FROM `branch` WHERE `STATUS` = 'Active'");
                        if ($branch_sql->num_rows > 0) {
                            while ($branch = $branch_sql->fetch_assoc()) {
                                echo '<option value="' . $branch['ID'] . '">' . $branch['BRANCH'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Please Select Branch.
                    </div>
                    <label class="product-add-label">Select Branch</label>
                </div>
                <div class="contents-input-container">
                    <input type="date" id="edit_date" class="form-control" name="edit_date" required max="<?php echo date('Y-m-d'); ?>">
                    <label class="product-add-label">Delivery Date</label>
                    <div class="invalid-feedback">
                        Advance date are not allowed.
                    </div>
                </div>
                <center class="add-bnts">
                    <button type="reset" id="so-edit-cancel" class="btn btn-dark">Cancel</button>
                    <button type="submit" id="so-edit-save" class="btn btn-primary">Save</button>
                </center>
            </form>

            <div class="message-container">
                <?php
                $messages = "SELECT * FROM messages ORDER BY LATEST_MESS_TIMESTAMP DESC";
                $messages_result = $conn->query($messages);
                if ($messages_result->num_rows > 0) {
                    while ($messages_row = $messages_result->fetch_assoc()) {
                        $mess_id = $messages_row['MESS_ID'];

                        $customer = "SELECT * FROM customer_user WHERE CUST_ID = $mess_id";
                        $customer_result = $conn->query($customer);
                        $customer_row = $customer_result->fetch_assoc();
                ?>

                        <div class="message-content <?php echo "message" . $customer_row['CUST_ID'] . "message" ?>">
                            <div class="message-header">
                                <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>" alt="avatar">
                                <p><?php echo $customer_row['FIRST_NAME'] . " " . $customer_row['LAST_NAME'] ?></p>
                                <button class="close-message"><i class="fa-solid fa-circle-xmark"></i></button>
                            </div>
                            <div id="message-container" class="message-text">
                                <?php

                                $messages_content = "SELECT * FROM message WHERE MESS_ID = $mess_id ORDER BY TIMESTAMP ASC";
                                $messages_content_result = $conn->query($messages_content);

                                if ($messages_content_result->num_rows > 0) {
                                    while ($messages_content_row = $messages_content_result->fetch_assoc()) {
                                        if ($messages_content_row['MESS_ID'] === $messages_content_row['SENDER_ID']) {
                                            $messageFrom = "SELECT * FROM customer_user WHERE CUST_ID = {$messages_content_row['MESS_ID']}";
                                            $messageFrom_result = $conn->query($messageFrom);
                                            $senderCustomer = $messageFrom_result->fetch_assoc();

                                            $sender = $senderCustomer['FIRST_NAME'] . " " . $senderCustomer['LAST_NAME'];
                                        } else {
                                            $sender = "GOrder";
                                        }
                                ?>
                                        <div>
                                            <article><?php echo $sender ?></article>
                                            <p><?php echo $messages_content_row['MESSAGE_BODY'] ?></p>
                                        </div>

                                <?php

                                    }
                                }

                                ?>
                            </div>
                            <form class="send-message send-message-form" id="send-message">
                                <input type="hidden" value="<?php echo $emp['EMP_ID'] ?>" name="sender_id">
                                <input type="hidden" value="<?php echo $mess_id ?>" name="message_id">
                                <input type="text" name="message" class="textfield">
                                <button type="submit" name="send" class="send"><i class="fa-solid fa-paper-plane"></i></button>
                            </form>
                        </div>

                <?php
                    }
                }
                ?>
            </div>

            <div class="modal" tabindex="-1" role="dialog" id="myModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to dispose this stock out report?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="delete-this-stock-out" data-id="">Delete</button>
                            <button type="button" class="btn btn-secondary" id="close-delete-this-stock-out" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <p class="emptype-name"><?php echo $emp['EMP_TYPE'] . " : " . $emp['FIRST_NAME'] . " " . $emp["MIDDLE_INITIAL"] . " " . $emp['LAST_NAME'] ?></p>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
        <script src="../js/side-nav-dropdown.js"></script>
        <script src="../js/nav-avatar-dropdown.js"></script>
        <script src="../js/nav-notif-dropdown.js"></script>
        <script src="../js/nav-message-dropdown.js"></script>
        <script src="../js/side-nav-show.js"></script>
        <script src="../js/message.js"></script>
        <script src="../js/mess-send.js"></script>
        <script src="../js/mess-scroll.js"></script>
        <script src="../js/notifications.js"></script>
        <script src="../js/products-stock-out.js"></script>
        <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
        <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>