<?php
error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
} else {
    header("Location: ../index.php");
    exit;
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
    <link rel="stylesheet" href="../css/pos-nav.css">
    <link rel="stylesheet" href="../css/order.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Orders</title>
</head>

<body>
    <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists" && $emp['EMP_STATUS'] == "active") : ?>
        <nav class="top-nav bg-dark">

            <i class="fa-solid fa-bars menu"></i>

            <img class="logo" src="../img/ggd-text-logo.png" alt="Golden Gate Drugstore">

            <div class="top-navigations">
                <a href="pos.php">POS</a>
                <a href="orders.php" class="top-navigations-active">Orders</a>
                <a href="sales.php">Sales</a>
                <a href="return.php">Return</a>
                <?php
                if ($emp['EMP_TYPE'] === 'Admin') {
                ?>
                    <a href="../admin/dashboard.php">Dashboard</a>
                <?php
                }
                ?>
            </div>

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
                    <?php
                    if ($emp['EMP_TYPE'] === 'PA') {
                    ?>
                        <em class="admin-em" style="font-size: 11px;">
                            Pharmacy Assistant
                        </em>
                    <?php
                    } elseif ($emp['EMP_TYPE'] === 'Pharmacists') {
                    ?>
                        <em class="admin-em" style="font-size: 12px; margin-right: 5px;">
                            Pharmacists
                        </em>
                    <?php
                    } else {
                    ?>
                        <em class="admin-em">
                            <?php echo $emp['EMP_TYPE']; ?>
                        </em>
                    <?php
                    }
                    ?>

                    <img src="../img/userprofile/<?php echo $emp['PICTURE'] ?>">
                </li>
                <div class="avatar-dropdown-container">
                    <a href="
                    <?php echo $emp['EMP_TYPE'] === 'Admin' ? '../admin/avatar-profile.php' : 'profile.php' ?>"><i class="fa-solid fa-user"></i>Profile</a>
                    <hr>
                    <a href="../process/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
                </div>
            </ul>


        </nav>
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="11">
                            <center>
                                <select class="today-this-week-sales form-control" id="orders_filter">
                                    <option value="Waiting">Pending</option>
                                    <option value="Accepted">Accepted</option>
                                    <option value="For-Delivery">For Delivery</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Ready To Pick Up">Ready To Pick Up</option>
                                    <option value="Picked Up">Picked Up</option>
                                    <option value="Refund Online Payment">Refund Online Payment</option>
                                </select>
                            </center>
                        </th>
                    </tr>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Payment Type</th>
                        <th>Delivery Type</th>
                        <th>Address</th>
                        <th>Order On</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody id="orders_result">

                </tbody>
            </table>
        </div>


        <div class="main">

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
        <script src="../js/message.js"></script>
        <script src="../js/mess-send.js"></script>
        <script src="../js/mess-scroll.js"></script>
        <script src="../js//orders.js"></script>
        <script src="../js/notifications.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>