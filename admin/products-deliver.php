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
    <link rel="stylesheet" href="../css/products-deliver.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Deliver</title>
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
                    <!-- <?php
                            //   $sql = "SELECT * FROM tblproducts
                            //   WHERE product_qty < critical_level";
                            //   $result = $conn->query( $sql);

                            //   if($result->num_rows >0)
                            //   {
                            // 
                            ?>
            //         <span class="badge rounded-pill badge-notification bg-danger"><?php echo $result->num_rows ?></span>
            //         <?php
                        //   } -->
                        ?> -->
                </li>
                <div class="notification-dropdown-container">
                    <center class="text-light">No notification found</center>
                </div>

                <li class="avatar-dropdown dropdown"><img src="../img/userprofile/<?php echo $emp['PICTURE'] ?>"></li>
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
                <a href="products-deliver.php" class="nav-active"><i class="fa-solid fa-truck"></i>Deliver</a>
                <a href="products-supplier.php"><i class="fa-solid fa-building"></i>Supplier</a>
                <a href="products-return.php"><i class="fa-solid fa-rotate-left"></i>Returns</a>
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
                <a href="reports-attendance.php"><i class="fa-solid fa-clipboard-user"></i>Attendance</a>
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

            <div class="search-select-delivery-container">

                <form class="search-container" id="search-form" method="get">
                    <input type="text" name="search" id="search-input" value="<?php
                                                                                if (isset($_GET['search'])) {
                                                                                    echo trim($_GET['search']);
                                                                                }
                                                                                ?>" placeholder="Search Delivery ID...">
                    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="select-delivery-only">
                    <div class="contents-input-container">
                        <select class="supplier-select-display" name="supplier_select" id="supplier-select">
                            <option value="all">All</option>
                            <?php

                            $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_STATUS = 'active'";
                            $supplier_result = $conn->query($supplier_sql);
                            if ($supplier_result->num_rows > 0) {
                                while ($supplier = $supplier_result->fetch_assoc()) {
                            ?>

                                    <option value="<?php echo $supplier['SUPPLIER_ID'] ?>" <?= (isset($_GET['supplier']) && $_GET['supplier'] == $supplier['SUPPLIER_ID']) ? 'selected' : '' ?>><?php echo $supplier['NAME'] ?></option>

                            <?php
                                }
                            }

                            ?>
                        </select>
                        <label class="product-add-label">Select Supplier</label>
                    </div>

                    <div class="contents-input-container">
                        <select class="by-filtering" name="deliver_filtering" id="by-filtering">
                            <option value="" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == '') ? 'selected' : '' ?>>Default</option>
                            <option value="by_price_asc" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == 'by_price_asc') ? 'selected' : '' ?>>Low to High</option>
                            <option value="by_price_desc" <?= (isset($_GET['sort_by']) && $_GET['sort_by'] == 'by_price_desc') ? 'selected' : '' ?>>High to Low</option>
                        </select>
                        <label class="product-add-label">Sort by Price</label>
                    </div>
                </div>

            </div>


            <div class="delivery-list-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Supplier</th>
                            <th>Delivery Date</th>
                            <th>Delivery Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (isset($_GET['search']) && $_GET['search'] != null) {
                            $deliveryID = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

                            $delivery_id_sql = "SELECT * FROM delivery WHERE DELIVERY_ID LIKE '%$deliveryID%' AND DELIVERY_STATUS = 'active'";
                            $delivery_id_result = $conn->query($delivery_id_sql);

                            if ($delivery_id_result->num_rows > 0) {
                                while ($row = $delivery_id_result->fetch_assoc()) {
                                    $supID = $row['SUPPLIER_ID'];
                                    $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $supID AND DELIVERY_STATUS = 'active'";
                                    $supplier_result = $conn->query($supplier_sql);
                                    $supplier = $supplier_result->fetch_assoc();
                        ?>

                                    <tr>
                                        <td><?php echo $row['DELIVERY_ID'] ?></td>
                                        <td><?php echo $supplier['NAME'] ?></td>
                                        <td><?php echo $row['DELIVERY_DATE'] ?></td>
                                        <td><?php echo $row['DELIVERY_PRICE'] ?></td>
                                        <td>
                                            <a href="delivered-products.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-eye"></i></a>
                                            <a href="#" class="edit-deliver-link <?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <a href="../admin/products-deliver-delete.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>

                                <?php
                                }
                            } else {
                                ?>
                                <tr class="search-not-found">
                                    <td colspan="6">Search Not Found</td>
                                </tr>
                                <?php
                            }
                        } elseif (isset($_GET['supplier'])) {
                            if (is_numeric($_GET['supplier'])) {
                                $supplierID = filter_input(INPUT_GET, 'supplier', FILTER_SANITIZE_NUMBER_INT);
                                if (isset($_GET['sort_by'])) {
                                    if ($_GET['sort_by'] == 'by_price_desc') {
                                        $deliver_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = $supplierID AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE DESC";
                                    } elseif ($_GET['sort_by'] == 'by_price_asc') {
                                        $deliver_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = $supplierID AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE ASC";
                                    } else {
                                        $deliver_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = $supplierID AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_DATE DESC";
                                    }
                                } else {
                                    $deliver_sql = "SELECT * FROM delivery WHERE SUPPLIER_ID = $supplierID AND DELIVERY_STATUS = 'active' ORDER BY DELIVERY_DATE DESC";
                                }
                                $deliver_result = $conn->query($deliver_sql);

                                if ($deliver_result->num_rows > 0) {
                                    while ($row = $deliver_result->fetch_assoc()) {
                                        $supID = $row['SUPPLIER_ID'];
                                        $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $supID";
                                        $supplier_result = $conn->query($supplier_sql);
                                        $supplier = $supplier_result->fetch_assoc();
                                ?>
                                        <tr>
                                            <td><?php echo $row['DELIVERY_ID'] ?></td>
                                            <td><?php echo $supplier['NAME'] ?></td>
                                            <td><?php echo $row['DELIVERY_DATE'] ?></td>
                                            <td><?php echo $row['DELIVERY_PRICE'] ?></td>
                                            <td>
                                                <a href="delivered-products.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-eye"></i></a>
                                                <a href="#" class="edit-deliver-link <?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                                <a href="../admin/products-deliver-delete.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr class="search-not-found">
                                        <td colspan="6">Search Not Found</td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                if ($_GET['supplier'] == 'all') {
                                    if (isset($_GET['sort_by'])) {
                                        if ($_GET['sort_by'] == 'by_price_desc') {
                                            $deliver_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE DESC";
                                        } elseif ($_GET['sort_by'] == 'by_price_asc') {
                                            $deliver_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_PRICE ASC";
                                        } else {
                                            $deliver_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_DATE DESC";
                                        }
                                    } else {
                                        $deliver_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_DATE DESC";
                                    }

                                    $deliver_result = $conn->query($deliver_sql);

                                    if ($deliver_result->num_rows > 0) {
                                        while ($row = $deliver_result->fetch_assoc()) {
                                            $supID = $row['SUPPLIER_ID'];
                                            $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $supID";
                                            $supplier_result = $conn->query($supplier_sql);
                                            $supplier = $supplier_result->fetch_assoc();
                                    ?>
                                            <tr>
                                                <td><?php echo $row['DELIVERY_ID'] ?></td>
                                                <td><?php echo $supplier['NAME'] ?></td>
                                                <td><?php echo $row['DELIVERY_DATE'] ?></td>
                                                <td><?php echo $row['DELIVERY_PRICE'] ?></td>
                                                <td>
                                                    <a href="delivered-products.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-eye"></i></a>
                                                    <a href="#" class="edit-deliver-link <?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                                    <a href="../admin/products-deliver-delete.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?> <tr class="search-not-found">
                                            <td colspan="6">Search Not Found</td>
                                        </tr><?php
                                            }
                                        } else {
                                                ?><tr class="search-not-found">
                                        <td colspan="6">Search Not Found</td>
                                    </tr><?php
                                        }
                                    }
                                } else {
                                    $deliver_sql = "SELECT * FROM delivery WHERE DELIVERY_STATUS = 'active' ORDER BY DELIVERY_DATE DESC";
                                    $deliver_result = $conn->query($deliver_sql);

                                    if ($deliver_result->num_rows > 0) {
                                        while ($row = $deliver_result->fetch_assoc()) {
                                            $supID = $row['SUPPLIER_ID'];
                                            $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $supID";
                                            $supplier_result = $conn->query($supplier_sql);
                                            $supplier = $supplier_result->fetch_assoc();

                                            ?>
                                    <tr>
                                        <td><?php echo $row['DELIVERY_ID'] ?></td>
                                        <td><?php echo $supplier['NAME'] ?></td>
                                        <td><?php echo $row['DELIVERY_DATE'] ?></td>
                                        <td><?php echo $row['DELIVERY_PRICE'] ?></td>
                                        <td>
                                            <a href="delivered-products.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-eye"></i></a>
                                            <a href="#" class="edit-deliver-link <?php echo $row['DELIVERY_ID'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <a href="../admin/products-deliver-delete.php?del_id=<?php echo $row['DELIVERY_ID'] ?>"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                        <?php
                                        }
                                    }
                                }


                        ?>
                    </tbody>
                </table>
            </div>
            <!--  -->
            <form class="deliver-add-form" id="deliverEditForm" action="../process/edit-deliver-process.php" method="post">
                <button id="closeEditDeliver" type="reset"><i class="fa-solid fa-xmark"></i></button>
                <h5>Edit Deliver</h5>
                <div class="contents-input-container supplier-select">
                    <input type="hidden" id="deliver-id-input" name="deliver_id" value="">
                    <select id="sub-category-select" name="supplier_id" required>
                        <?php

                        $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_STATUS = 'active'";
                        $supplier_result = $conn->query($supplier_sql);
                        if ($supplier_result->num_rows > 0) {
                            while ($supplier = $supplier_result->fetch_assoc()) {
                        ?>

                                <option value="<?php echo $supplier['SUPPLIER_ID'] ?>"><?php echo $supplier['NAME'] ?></option>

                        <?php
                            }
                        }

                        ?>
                    </select>
                    <label class="product-add-label">Supplier</label>
                </div>

                <div class="contents-input-container supplier-select">
                    <input type="date" id="deliveryDate" name="delivery_date" required max="<?php echo date('Y-m-d'); ?>">
                    <label class="product-add-label">Delivery Date</label>
                </div>

                <input type="submit" class="add-delivery btn btn-primary" name="edit_deliver" value="Save">
            </form>
            <!--  -->

            <button id="addDeliverOpen" class="addDeliver btn btn-primary" type="button"><i class="fa-solid fa-plus"></i>New Deliver</button>

            <form class="deliver-add-form" id="deliverAddForm" action="../process/add-deliver-process.php" method="post">
                <button id="closeAddDeliver" type="reset"><i class="fa-solid fa-xmark"></i></button>
                <h5>New Deliver</h5>
                <div class="contents-input-container supplier-select">
                    <select id="sub-category-select" name="supplier_id" required>
                        <?php

                        $supplier_sql = "SELECT * FROM supplier WHERE SUPPLIER_STATUS = 'active'";
                        $supplier_result = $conn->query($supplier_sql);
                        if ($supplier_result->num_rows > 0) {
                            while ($supplier = $supplier_result->fetch_assoc()) {
                        ?>

                                <option value="<?php echo $supplier['SUPPLIER_ID'] ?>"><?php echo $supplier['NAME'] ?></option>

                        <?php
                            }
                        }

                        ?>
                    </select>
                    <label class="product-add-label">Supplier</label>
                </div>

                <div class="contents-input-container supplier-select">
                    <input type="date" id="deliveryDate" name="delivery_date" required max="<?php echo date('Y-m-d'); ?>">
                    <label class="product-add-label">Delivery Date</label>
                </div>

                <input type="submit" class="add-delivery btn btn-primary" name="add_deliver" value="Add">
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
        <script src="../js/search-delivery.js"></script>
        <script src="../js/products-deliver-supplier.js"></script>
        <script src="../js/open-add-deliver.js"></script>
        <script src="../js/delivery-id-edit-form.js"></script>
        <script src="../js/edit-deliver-check-supplier.js"></script>




    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>