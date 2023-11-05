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
    <link rel="stylesheet" href="../css/maintenance-address.css">
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
                <a href="../message.php"><i class="fa-solid fa-message"></i>Messages</a>
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
                <a href="maintenance-address.php" class="nav-active"><i class="fa-solid fa-location-dot"></i>Address</a>
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

        <div class="alert alert-region bg-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            New region added successfully!
        </div>
        <div class="alert alert-region-failed bg-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Failed to add the region. Please try again.
        </div>

        <div class="alert alert-province bg-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            New province added successfully!
        </div>

        <div class="alert alert-province-failed bg-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Failed to add the province. Please try again.
        </div>

        <div class="alert alert-municipality bg-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            New municipality added successfully!
        </div>

        <div class="alert alert-municipality-failed bg-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Failed to add the municipality. Please try again.
        </div>

        <div class="alert alert-barangay bg-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            New barangay added successfully!
        </div>

        <div class="alert alert-barangay-failed bg-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Failed to add the barangay. Please try again.
        </div>
        <div class="alert alert-df-edit-success bg-success">
            Delivery Fee successfully edited.
        </div>
        <div class="alert alert-df-edit-not-success bg-success">
            Delivery Fee editing unsuccessful.
        </div>
        <div class="alert alert-add-disabled bg-success">
            Address Disabled
        </div>
        <div class="alert alert-add-address bg-success">
            Address Added
        </div>


        <div class="main">
            <center class="cat-center">Address</center>

            <div class="select-main-container">
                <form class="add-new-barangay-form">
                    <input type="text" class="form-control" id="txt-add-barangay" name="txt_add_region" placeholder="Add New Barangay" list="addressess-list" required>
                    <datalist id="addressess-list">
                        <?php
                        $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_STATUS != 'active'";
                        $bgy_result = $conn->query($bgy_sql);
                        if ($bgy_result->num_rows > 0) {
                            while ($bgy = $bgy_result->fetch_assoc()) {
                                $muni_id = $bgy['MUNICIPALITY_ID'];
                                $muni_result = $conn->query("SELECT MUNICIPALITY, PROVINCE_ID FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'");
                                $muni = $muni_result->fetch_assoc();

                                $province_id = $muni['PROVINCE_ID'];
                                $municipality = $muni['MUNICIPALITY'];

                                $province_result = $conn->query("SELECT PROVINCE, REGION_ID FROM province WHERE PROVINCE_ID = '$province_id'");
                                $prov = $province_result->fetch_assoc();
                                $province = $prov['PROVINCE'];
                        ?>
                                <option value="<?php echo $bgy['BARANGAY_ID'] ?>"><?php echo $bgy['BARANGAY'] . ' ,' . $municipality . ' ,' . $province ?></option>
                        <?php
                            }
                        }
                        ?>
                    </datalist>
                    <input type="submit" class="btn btn-primary" id="btn-add-region" name="btn_add_region" value="Add">
                </form>
                <div class="select-innner-container">
                    <div class="select-container">
                        <select class="form-control" id="select-region">
                            <option value=""></option>
                            <?php
                            $region_sql = "SELECT REGION_ID, REGION FROM region";
                            $region_result = $conn->query($region_sql);
                            if ($region_result->num_rows > 0) {
                                while ($region = $region_result->fetch_assoc()) {
                            ?>
                                    <option value="<?php echo $region['REGION_ID'] ?>"><?php echo $region['REGION'] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label>Region</label>
                    </div>
                    <div class="select-container">
                        <select class="form-control" id="select-province">
                            <option value=""></option>
                        </select>
                        <label>Province</label>
                    </div>
                    <div class="select-container">
                        <select class="form-control" id="select-municipality">
                            <option value=""></option>
                        </select>
                        <label>Municipality</label>
                    </div>
                </div>
            </div>


            <div class="address-table-container">
                <table class="table table-striped address-table">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Province</th>
                            <th>Municipality</th>
                            <th>Barangay</th>
                            <th>Delivery Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="address_container">

                    </tbody>
                </table>
            </div>

            <div class="edit-bgy-container">
                <a href="#" id="close-edit-bgy-container"><i class="fa-solid fa-xmark"></i></a>
                <center id="edit-bgy-title">Edit Delivery Fee In <span class="text-success" id="barangay-name"></span></center>
                <div class="input-container">
                    <input type="hidden" id="bgy-id">
                    <input type="number" id="df" class="form-control">
                    <label>Deliver Fee</label>
                </div>
                <input type="submit" value="Save" id="save-df" class="btn btn-primary">
            </div>

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
        <script src="../js/notifications.js"></script>
        <script src="../js/address-realtime.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>