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
    <link rel="stylesheet" href="../css/users-employee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Employee</title>
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
                <a href="products-stock-out.php"><i class="fa-solid fa-layer-group"></i>Stock Out</a>
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
                <a href="users-employee.php" class="nav-active"><i class="fa-solid fa-user-tie"></i>Employee</a>
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
                <a href="maintenance-payment-types.php"><i class="fa-solid fa-money-bill-transfer"></i>Payment Types</a>
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-user-pen"></i>Logs
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="logs-employee.php"><i class="fa-solid fa-user-tie"></i>Employee</a>
                <a href="logs-customer.php"><i class="fa-solid fa-people-group"></i>Customer</a>
            </div>

            <hr>

            <a href="../backup/backup.php" target="_blank"><i class="fa-solid fa-database"></i>Backup</a>
        </div>

        <div class="alert input_empty bg-danger">
            Please fill in all the necessary information.
        </div>
        <div class="alert invalid_email bg-danger">
            Invalid Email.
        </div>
        <div class="alert invalid_username bg-danger">
            Please enter a username with at least 8 characters.
        </div>
        <div class="alert acc_created bg-success">
            Account has been successfully created.
        </div>
        <div class="alert acc_created_unsuccessful bg-danger">
            Account creation unsuccessful.
        </div>

        <div class="alert bg-success alert-success">

        </div>
        <div class="alert bg-danger alert-danger">

        </div>

        <div class="main">

            <center>
                <p class="employee-title">Employee</p>
            </center>
            <div class="search-filter-container">
                <div class="search-container">
                    <input type="text" class="form-control" name="search_emp" id="search_emp" placeholder="Search...">
                    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div class="input">
                    <select id="emp_filter" class="form-control">
                        <option value="all">All</option>
                        <option value="Admin">Admin</option>
                        <option value="Pharmacists">Pharmacists</option>
                        <option value="PA">Pharmacy Assistant</option>
                        <option value="Rider">Rider</option>
                    </select>
                    <label>Employee Type</label>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Contact no.</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="emp-container">

                    </tbody>
                </table>
            </div>

            <form class="add-emp-form">
                <a href="#" class="close-add-emp-form" id="close-add-emp-form"><i class="fa-solid fa-xmark"></i></a>
                <center class="center-add-emp">New Employee</center>

                <div class="emp-form-f-row">
                    <div class="new-emp-input-container">
                        <input type="text" id="f_name" class="form-control" required>
                        <label>First Name</label>
                    </div>
                    <div class="new-emp-input-container">
                        <input type="text" id="l_name" class="form-control" required>
                        <label>Last Name</label>
                    </div>
                </div>

                <div class="emp-form-s-row">
                    <div class="new-emp-input-container">
                        <input type="text" id="mi" class="form-control">
                        <label>MI</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="suffix" class="form-control">
                            <option value=""></option>
                            <option value="Sr">Sr</option>
                            <option value="Jr">Jr</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                        </select>
                        <label>Suffix</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="sex" class="form-control">
                            <option disabled selected></option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                        <label>Sex</label>
                    </div>
                </div>
                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="date" class="form-control" id="birthday">
                        <label>Birthday</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="emp_type" class="form-control">
                            <option disabled selected></option>
                            <option value="Admin">Admin</option>
                            <option value="PA">Pharmacy Assistant</option>
                            <option value="Pharmacists">Pharmacists</option>
                            <option value="Rider">Rider</option>
                        </select>
                        <label>Role</label>
                    </div>
                </div>

                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="email" class="form-control" id="email">
                        <label>Email</label>
                    </div>
                    <div class="new-emp-input-container">
                        <input type="number" class="form-control" id="contact_no">
                        <label>Contact No</label>
                    </div>
                </div>
                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="text" class="form-control" id="address">
                        <label>Address</label>
                    </div>
                </div>
                <div class="emp-form-f-row">
                    <div class="new-emp-input-container">
                        <input type="text" class="form-control" id="username">
                        <label>Username</label>
                    </div>
                    <div class="new-emp-input-container">
                        <input type="password" class="form-control" id="password">
                        <label>Password</label>
                    </div>
                </div>
                <div class="emp-form-fi-row">
                    <div class="new-emp-input-container">
                        <center><input type="submit" id="btn-save-employee" class="btn btn-primary" value="Add"></input></center>
                    </div>
                </div>
            </form>

            <a href="#" id="new_emloyee" class="btn-add-employee btn btn-primary">Add Employee</a>

            <!-- Edit Form -->
            <form class="edit-emp-form">
                <a href="#" class="close-add-emp-form" id="close-edit-emp-form"><i class="fa-solid fa-xmark"></i></a>
                <center class="center-add-emp">Edit Employee</center>

                <div class="emp-form-f-row">
                    <div class="new-emp-input-container">
                        <input type="text" id="edit_f_name" class="form-control" required>
                        <label>First Name</label>
                    </div>
                    <div class="new-emp-input-container">
                        <input type="text" id="edit_l_name" class="form-control" required>
                        <label>Last Name</label>
                    </div>
                </div>

                <div class="emp-form-s-row">
                    <div class="new-emp-input-container">
                        <input type="text" id="edit_mi" class="form-control">
                        <label>MI</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="edit_suffix" class="form-control">
                            <option value=""></option>
                            <option value="Sr">Sr</option>
                            <option value="Jr">Jr</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                        </select>
                        <label>Suffix</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="edit_sex" class="form-control">
                            <option disabled selected></option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                        <label>Sex</label>
                    </div>
                </div>
                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="date" class="form-control" id="edit_birthday">
                        <label>Birthday</label>
                    </div>
                    <div class="new-emp-input-container">
                        <select id="edit_emp_type" class="form-control">
                            <option disabled selected></option>
                            <option value="Admin">Admin</option>
                            <option value="PA">Pharmacy Assistant</option>
                            <option value="Pharmacists">Pharmacists</option>
                            <option value="Rider">Rider</option>
                        </select>
                        <label>Role</label>
                    </div>
                </div>

                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="email" class="form-control" id="edit_email">
                        <label>Email</label>
                    </div>
                    <div class="new-emp-input-container">
                        <input type="number" class="form-control" id="edit_contact_no">
                        <label>Contact No</label>
                    </div>
                </div>
                <div class="emp-form-t-row">
                    <div class="new-emp-input-container">
                        <input type="text" class="form-control" id="edit_address">
                        <label>Address</label>
                    </div>
                </div>
                <div class="emp-form-f-row">
                    <div class="new-emp-input-container">
                        <input type="text" class="form-control" id="edit_username">
                        <label>Username</label>
                    </div>
                    <!-- <div class="new-emp-input-container">
                        <input type="password" class="form-control" id="edit_password">
                        <label>Password</label>
                    </div> -->
                </div>
                <div class="emp-form-fi-row">
                    <div class="new-emp-input-container">
                        <center><input type="submit" id="edit_btn-save-employee" data-id="" class="btn btn-primary" value="Save"></input></center>
                    </div>
                </div>
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
        <script src="../js/emp-realtime.js"></script>
        <script src="../js/notifications.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>