<?php
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
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/loading.css">
    <link rel="stylesheet" href="../css/users-customer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder</title>
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
                    $messages = "SELECT * FROM messages ORDER BY LATEST_MESS_TIMESTAMP ASC";
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
                                <a href="#" class="dropdown-message <?php echo $customer_row['FIRST_NAME'] . $customer_row['LAST_NAME'] ?>">
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
                    <a href="avatar-settings.php"><i class="fa-solid fa-gear"></i>Settings</a>
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
            </div>

            <hr>

            <button class="dropdown-btn">
                <i class="fa-solid fa-users"></i>Users
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <a href="users-employee.php"><i class="fa-solid fa-user-tie"></i>Employee</a>
                <a href="users-customer.php" class="nav-active"><i class="fa-solid fa-people-group"></i>Customer</a>
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
        </div>

        <div class="main">

            <div class="alert bg-danger alert-deact">
                <p class="">Account Deactivated</p>
            </div>
            <div class="alert bg-success alert-act">
                <p class="">Account Activated</p>
            </div>
            <div class="alert bg-success alert-edited">
                <p class="">Editing Success</p>
            </div>
            <div class="alert bg-danger alert-not-edited">
                <p class="">Editing Unsuccessful</p>
            </div>

            <div class="alert bg-warning enter-fname">
                <p class="">Please enter a first name.</p>
            </div>
            <div class="alert bg-warning enter-lname">
                <p class="">Please enter a last name.</p>
            </div>
            <div class="alert bg-warning enter-bgy">
                <p class="">Please select a barangay.</p>
            </div>
            <div class="alert bg-warning enter-unit">
                <p class="">Please enter the unit/street/village.</p>
            </div>
            

            <center>
                <p class="employee-title">Customers</p>
            </center>
            <div class="search-filter-container">
                <div class="search-container">
                    <input type="text" class="form-control" name="search_emp" id="search_cust" placeholder="Search...">
                    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div class="input">
                    <select id="cust_filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $discount_sql = "SELECT DISCOUNT_NAME, DISCOUNT_ID FROM discount WHERE DISCOUNT_STATUS = 'active'";
                        if ($discount_result = $conn->query($discount_sql)) {
                            if ($discount_result->num_rows > 0) {
                                while ($discount = $discount_result->fetch_assoc()) {
                        ?>
                                    <option value="<?php echo $discount['DISCOUNT_ID']; ?>"><?php echo $discount['DISCOUNT_NAME']; ?></option>
                        <?php
                                }
                            }
                        }
                        ?>
                    </select>
                    <label>Discount Type</label>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cust ID</th>
                            <th>Name</th>
                            <th>Contact no.</th>
                            <th>Discount Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="customer-container">

                    </tbody>
                </table>
            </div>

            <form id="frm-edit-cust" class="frm-edit-cust">
                <a href="#" id="close-frm-edit-cust"><i class="fa-solid fa-xmark"></i></a>
                <center id="edit-cust-title" class="edit-cust-title"></center>
                <div class="first-div">
                    <img src="" id="cust-photo">
                    <input type="hidden" value="" id="cust_id_hidden">
                    <div class="cust-details">
                        <div class="cust-details-f-row">
                            <div class="input-container">
                                <input type="text" class="form-control" id="fname" value="">
                                <label>First Name</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="lname" value="">
                                <label>Last Name</label>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="mi" value="">
                                <label>MI</label>
                            </div>
                            <div class="input-container">
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
                        </div>
                        <div class="cust-details-s-row">
                            <div class="input-container">
                                <select id="sex" class="form-control">
                                    <option value="m">Male</option>
                                    <option value="f">Female</option>
                                </select>
                                <label>Sex</label>
                            </div>
                            <div class="input-container">
                                <input type="date" id="birthday" class="form-control">
                                <label>Birthday</label>
                            </div>
                            <div class="input-container">
                                <select id="discount-type" class="form-control">
                                    <option value=""></option>
                                    <?php
                                    $discount_sql = "SELECT DISCOUNT_ID, DISCOUNT_NAME FROM discount WHERE DISCOUNT_STATUS = 'active'";
                                    if ($discount_result = $conn->query($discount_sql)) {
                                        if ($discount_result->num_rows > 0) {
                                            while ($discount = $discount_result->fetch_assoc()) {
                                    ?>
                                                <option value="<?php echo $discount['DISCOUNT_ID'] ?>"><?php echo $discount['DISCOUNT_NAME'] ?></option>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Customer Type</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="second-div">
                    <div class="region-province">
                        <div class="input-container">
                            <select id="region" class="form-control">
                                <?php
                                $region_sql = "SELECT REGION_ID, REGION FROM region";
                                if ($region_result = $conn->query($region_sql)) {
                                    if ($region_result->num_rows > 0) {
                                        while ($region = $region_result->fetch_assoc()) {
                                ?>
                                            <option value="<?php echo $region['REGION_ID'] ?>"><?php echo $region['REGION'] ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <label>Region</label>
                        </div>
                        <div class="input-container">
                            <select id="province" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Province</label>
                        </div>
                    </div>
                    <div class="municipality-barangay">
                        <div class="input-container">
                            <select id="municipality" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Municipality</label>
                        </div>
                        <div class="input-container">
                            <select id="barangay" class="form-control">
                                <option value=""></option>
                            </select>
                            <label>Barangay</label>
                        </div>
                    </div>
                    <div class="unit-st-container">
                        <div class="input-container">
                            <input type="text" id="unit" class="form-control">
                            <label>Unit No. / Street / Village</label>
                        </div>
                    </div>
                </div>
                <div class="third-div">
                    <div class="third-div-f-row">
                        <div class="input-container">
                            <input type="text" id="username" class="form-control">
                            <label>Username</label>
                        </div>
                        <div class="input-container">
                            <input type="email" id="email" class="form-control">
                            <label>Email</label>
                        </div>
                        <div class="input-container">
                            <input type="number" id="contact-no" class="form-control">
                            <label>Contact No.</label>
                        </div>
                    </div>
                    <div class="third-div-s-row">
                        <a href="#" id="btn-cancel" class="btn btn-dark">Cancel</a>
                        <input type="submit" id="btn-submit" class="btn btn-primary" value="Save">
                    </div>
                </div>
            </form>

            <div class="message-container">
                <?php
                $messages = "SELECT * FROM messages";
                $messages_result = $conn->query($messages);
                if ($messages_result->num_rows > 0) {
                    while ($messages_row = $messages_result->fetch_assoc()) {
                        $mess_id = $messages_row['MESS_ID'];

                        $customer = "SELECT * FROM customer_user WHERE CUST_ID = $mess_id";
                        $customer_result = $conn->query($customer);
                        $customer_row = $customer_result->fetch_assoc();
                ?>

                        <div class="message-content <?php echo $customer_row['FIRST_NAME'] . $customer_row['LAST_NAME'] . "message" ?>">
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
                            <form class="send-message" id="send-message">
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
        <script src="../js/notifications.js"></script>
        <script src="../js/users-customer.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>