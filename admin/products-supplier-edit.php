<?php 
error_reporting(0);
session_start();

if(isset($_SESSION['id']))
{
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
        <link rel="stylesheet" href="../css/products-supplier-edit.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
        <title>GOrder | Edit Supplier</title>
    </head>
    <body>
        <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active" && isset($_GET['sup_id'])): ?>
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
                    if($messages_result->num_rows > 0)
                    { 
                    ?>
                    <?php 
                        
                        while($messages_row = $messages_result->fetch_assoc())
                        {
                            $mess_id = $messages_row['MESS_ID'];
                            $message = "SELECT * FROM message WHERE MESS_ID = $mess_id";
                            $message_result = $conn->query($message);
            
                            if($message_result->num_rows > 0)
                            {
                                $latest_message = "SELECT * FROM message WHERE MESS_ID = $mess_id AND TIMESTAMP = ( SELECT MAX(TIMESTAMP) FROM message WHERE MESS_ID = $mess_id)";
                                $latest_message_result = $conn->query($latest_message);
                                $latest_message_row = $latest_message_result->fetch_assoc();

                                $message_id = $latest_message_row['MESS_ID'];

                                $customer = "SELECT * FROM customer_user WHERE CUST_ID = $message_id";
                                $customer_result = $conn->query($customer);
                                $customer_row = $customer_result->fetch_assoc();
                        
                    ?>
                                <a href="#" class="dropdown-message <?php echo $customer_row['FIRST_NAME'].$customer_row['LAST_NAME'] ?>">
                                    <div class="from">
                                        <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>">
                                        <h3><?php echo $customer_row['FIRST_NAME']." ".$customer_row['LAST_NAME']; ?></h3>
                                    </div>
                                    <p><?php  echo $latest_message_row['MESSAGE_BODY']?></p>
                                    <article><?php  echo $latest_message_row['TIMESTAMP']?></article>
                                </a>
                                <hr>
                    <?php    
                            }
                            
                        }
                    }
                    else
                    {

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
                    <em class="admin-em"><?php  echo $emp['EMP_TYPE'] ?></em>
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
            <i class="fa-solid fa-users"></i><?php echo $emp['FIRST_NAME']." ".$emp['LAST_NAME']?>
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
                <a href="products-supplier.php" class="nav-active"><i class="fa-solid fa-building"></i>Supplier</a>
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
    </div>

    <div class="main">

        <?php 
            $suppID = $_GET['sup_id'];
            $supp_sql = "SELECT * FROM supplier WHERE SUPPLIER_ID = $suppID";
            $supplier_sql_result = $conn->query($supp_sql);
            
            if($supplier_sql_result->num_rows > 0)
            {
                $supplier = $supplier_sql_result->fetch_assoc();
        ?>

            <div class="supp-supplier-edit-contianer">

                <a href="products-supplier.php" class="back-to-supplier">Back To Supplier<i class="fa-solid fa-arrow-left-long"></i></a>
                <form class="supp-edit-container" action="../process/edit-supplier-process.php" method="post">
                    <p class="supp-id"><?php echo $supplier['SUPPLIER_ID'] ?></p>
                    <center class="edit-product-text pt-3">Edit Supplier</center>
                    <div class="contents-input-container">
                        <input type="text" class="supp-name" name="supp_name" value="<?php echo $supplier['NAME'] ?>" maxlength="55" required>
                        <label class="product-edit-label">Supplier Name</label>
                    </div>

                    <div class="contents-input-container">
                        <input type="text" class="supp-address" name="supp_address" value="<?php echo $supplier['ADDRESS'] ?>" maxlength="200">
                        <label class="product-edit-label">Address</label>
                    </div>

                    <div class="contents-input-container">
                        <input type="text" class="contact-person" name="contact_person" value="<?php echo $supplier['CONTACT_PERSON'] ?>" maxlength="55" required pattern="[a-zA-Z ]+">
                        <label class="product-edit-label">Contact Person</label>
                    </div>

                    <div class="contents-input-container">
                        <input type="text" class="contact-number" name="contact_number" value="<?php echo $supplier['CONTACT_NO'] ?>" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                        <label class="product-edit-label">Contact Number</label>
                    </div>

                    <input type="hidden" name="supp_id" value="<?php echo $supplier['SUPPLIER_ID'] ?>">

                    <div class="contents-input-container">
                        <input type="submit" name="edit_product" class="edit-product btn btn-primary" value="Save Changes">
                    </div>
                </form>
            </div>

        <?php 

            }
            else
            {
                echo "<center class='no-supp-found'>No Supplier Found <a href='products-supplier.php' class='link-Go-to-Supp'>Go to supplier</a></center>";
            }
        ?>

        <div class="message-container">
            <?php 
                $messages = "SELECT * FROM messages";
                $messages_result = $conn->query($messages);
                if($messages_result->num_rows > 0)
                {
                    while($messages_row = $messages_result->fetch_assoc())
                    {
                            $mess_id = $messages_row['MESS_ID'];

                            $customer = "SELECT * FROM customer_user WHERE CUST_ID = $mess_id";
                            $customer_result = $conn->query($customer);
                            $customer_row = $customer_result->fetch_assoc();
            ?>
                                
                                <div class="message-content <?php echo $customer_row['FIRST_NAME'].$customer_row['LAST_NAME']."message" ?>">
                                    <div class="message-header">
                                        <img src="../img/userprofile/<?php echo $customer_row['PICTURE'] ?>" alt="avatar">
                                        <p><?php echo $customer_row['FIRST_NAME']." ".$customer_row['LAST_NAME'] ?></p>
                                        <button class="close-message"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="message-container" class="message-text">
                                        <?php 

                                        $messages_content = "SELECT * FROM message WHERE MESS_ID = $mess_id ORDER BY TIMESTAMP ASC";
                                        $messages_content_result = $conn->query($messages_content);
                                        
                                        if($messages_content_result->num_rows > 0)
                                        {
                                            while($messages_content_row = $messages_content_result->fetch_assoc())
                                            {
                                                if($messages_content_row['MESS_ID'] === $messages_content_row['SENDER_ID'])
                                                {
                                                    $messageFrom = "SELECT * FROM customer_user WHERE CUST_ID = {$messages_content_row['MESS_ID']}";
                                                    $messageFrom_result = $conn->query($messageFrom);
                                                    $senderCustomer = $messageFrom_result->fetch_assoc();

                                                    $sender = $senderCustomer['FIRST_NAME']." ".$senderCustomer['LAST_NAME'];
                                                }
                                                else
                                                {
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

    <p class="emptype-name"><?php echo $emp['EMP_TYPE'] ." : ". $emp['FIRST_NAME'] ." ". $emp["MIDDLE_INITIAL"] ." ". $emp['LAST_NAME']?></p>

        <script  src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
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
        
    <?php else: ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
    </body>
</html>