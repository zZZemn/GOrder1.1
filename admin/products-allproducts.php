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
    <link rel="stylesheet" href="../css/products-allproducts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | All Products</title>
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
                <a href="products-allproducts.php" class="nav-active"><i class="fa-solid fa-prescription"></i>All Products</a>
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

        <div class="alert invalid-upload bg-danger">
            Invalid uploading image.
        </div>
        <div class="alert invalid-upload bg-danger">
            Invalid image size.
        </div>
        <div class="alert deletion-unsuccessful bg-danger">
            Deletion unsuccessfull.
        </div>
        <div class="alert product-deleted bg-success">
            Product Deleted
        </div>

        <div class="main">

            <div class="search-bar-category-pic">
                <div class="search-container" id="search-form" method="get">
                    <input type="text" name="search" id="search-input" value="<?php
                                                                                if (isset($_GET['search'])) {
                                                                                    echo trim($_GET['search']);
                                                                                }
                                                                                ?>" placeholder="Search Products..." class="form-control">
                    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div class="category-pick">
                    <?php

                    $categories = "SELECT * FROM category";
                    $categories_result = $conn->query($categories);
                    ?>

                    <div class="contents-input-container category_label">
                        <select id="select1" class="form-control">
                            <option value="all">All</option>
                            <?php
                            if ($categories_result->num_rows > 0) {
                                while ($row = $categories_result->fetch_assoc()) {
                            ?>

                                    <option value="<?php echo $row['CAT_ID'] ?>" <?php
                                                                                    if (isset($_GET['CAT_ID'])) {
                                                                                        if ($_GET['CAT_ID'] == $row['CAT_ID']) {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    }
                                                                                    ?>>
                                        <?php echo $row['CAT_NAME'] ?></option>

                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label class="product-add-label">Category</label>
                    </div>

                    <div class="contents-input-container category_label">

                        <select id="select2" class="form-control">
                            <option value="all">All</option>

                        </select>
                        <label class="product-add-label">Sub Category</label>
                    </div>
                </div>
            </div>

            <button id="addProduct" class="addProduct btn btn-primary"><i class="fa-solid fa-plus"></i><span>Add Product.</span></button>

            <form id="addingProduct-form" method="post" action="../process/add-product-process.php" enctype="multipart/form-data">
                <button id="closeAddProduct" type="button"><i class="fa-solid fa-xmark"></i></button>
                <h1 class="add-product-label">New Product</h1>
                <div class="add-product-f-row">
                    <div class="contents-input-container product-name">
                        <input class="form-control" type="text" name="product_name" maxlength="49" required>
                        <label class="product-add-label">Product Name</label>
                    </div>
                    <div class="contents-input-container product-code">
                        <input class="form-control" type="number" name="product_code" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="29" />
                        <label class="product-add-label">Product Code</label>
                    </div>
                </div>
                <div class="add-product-s-row">
                    <div class="contents-input-container unit-meas-add">
                        <input step="any" class="form-control" type="number" name="mg">
                        <label class="product-add-label">mg</label>
                    </div>
                    <div class="contents-input-container unit-meas-add">
                        <input step="any" class="form-control" type="number" name="g">
                        <label class="product-add-label">g</label>
                    </div>
                    <div class="contents-input-container unit-meas-add">
                        <input step="any" class="form-control" type="number" name="ml">
                        <label class="product-add-label">ml</label>
                    </div>
                    <div class="contents-input-container critical-level-add">
                        <input class="form-control" type="number" name="critical_level_add" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" />
                        <label class="product-add-label">Critical Level</label>
                    </div>
                    <div class="contents-input-container selling-price-add">
                        <input class="form-control" type="number" name="selling_price_add" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="5" />
                        <label class="product-add-label">Selling Price</label>
                    </div>
                </div>
                <div class="add-product-t-row">
                    <div class="contents-input-container add-product-cat">
                        <select class="form-control" id="category-select" name="cat">
                            <?php
                            $categories = "SELECT * FROM category";
                            $categories_result = $conn->query($categories);

                            if ($categories_result->num_rows > 0) {
                                while ($row = $categories_result->fetch_assoc()) {
                            ?>
                                    <option value="<?php echo $row['CAT_ID'] ?>"><?php echo $row['CAT_NAME'] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label class="product-add-label">Category</label>
                    </div>

                    <div class="contents-input-container add-product-sub-cat">
                        <select class="form-control" id="sub-category-select" name="sub_cat">

                        </select>
                        <label class="product-add-label">Sub Category</label>
                    </div>
                </div>
                <div class="add-product-fth-row">
                    <div class="contents-input-container description">
                        <textarea class="form-control" name="description" maxlength="99"></textarea>
                        <label class="product-add-label">Description</label>
                    </div>
                    <div class="add-product-fth-row-right">
                        <div class="form-check form-switch pres-vat">
                            <input class="form-check-input" type="checkbox" id="prescribe" name="prescribe">
                            <label class="form-check-label" for="prescribe">Prescribe</label>
                        </div>
                        <div class="form-check form-switch pres-vat">
                            <input class="form-check-input" type="checkbox" id="vatable" name="vatable">
                            <label class="form-check-label" for="vatable">Vatable</label>
                        </div>
                        <div class="form-check form-switch pres-vat">
                            <input class="form-check-input" type="checkbox" id="discountable" name="discountable">
                            <label class="form-check-label" for="discountable">Discountable</label>
                        </div>
                        <div class="form-check form-switch pres-vat">
                            <input class="form-check-input" type="checkbox" id="SellInGorder" name="SellInGorder">
                            <label class="form-check-label" for="SellInGorder">Sell Online</label>
                        </div>
                        <div class="upload-pic">
                            <input type="file" class="form-control" name="product_image" id="customFile">
                        </div>
                    </div>
                </div>
                <div class="btns">
                    <button id="cancelAddProduct" type="reset" class="btn btn-light">Cancel</button>
                    <input type="submit" name="addProduct" value="Add" class="btn btn-primary">
                </div>
            </form>

            <div class="all-producst-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="pro-code">Product Code</th>
                            <th>Product Name</th>
                            <th>mg</th>
                            <th>g</th>
                            <th>ml</th>
                            <th class="selling-price">Selling Price</th>
                            <th class="">Qty</th>
                            <th>Critical Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="products-container">

                    </tbody>
                </table>
            </div>

            <button type="button" id="printReport" class="btn btn-primary">Print Report</button>

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

        <div class="modal" tabindex="-1" role="dialog" id="myModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="delete-this-product" data-product_id="">Delete</button>
                        <button type="button" class="btn btn-secondary" id="close-delete-this-product" data-dismiss="modal">Close</button>
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
        <script src="../js/all-products-add-getting-selected-category.js"></script>
        <script src="../js/open-add-product-form.js"></script>
        <script src="../js/notifications.js"></script>
        <script src="../js/products-allproducts.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>