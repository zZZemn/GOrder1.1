<?php
// error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    if (isset($_GET['data'])) {
        if ($_GET['data'] === 'usersMessageContainer') {
            $sql = "SELECT cu.*, m.MESS_ID FROM `messages` m JOIN customer_user cu ON m.MESS_ID = cu.CUST_ID";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
?>
                    <button type="button" class="btnViewMessage" data-id="<?= $row['MESS_ID'] ?>"><img src="img/userprofile/<?= $row['PICTURE'] ?>"><?= $row['FIRST_NAME'] . ' ' . $row['MIDDLE_INITIAL'] . ' ' . $row['LAST_NAME'] ?></button>
                <?php
                }
            }
        } elseif ($_GET['data'] === 'usersMessageProfileContainer') {
            $id = $_GET['id'];
            $sql = "SELECT * FROM `customer_user` WHERE `CUST_ID` = '$id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                ?>
                <input type="hidden" id="messageId" value="<?= $row['CUST_ID'] ?>">
                <img src="img/userprofile/<?= $row['PICTURE'] ?>">
                <p><?= $row['FIRST_NAME'] . ' ' . $row['MIDDLE_INITIAL'] . ' ' . $row['LAST_NAME'] ?></p>
                <?php
            }
        } elseif ($_GET['data'] === 'messageContentContainer') {
            $id = $_GET['id'];
            $sql = "SELECT m.*, e.* FROM `message` m LEFT JOIN `employee` e ON m.SENDER_ID = e.EMP_ID WHERE m.MESS_ID = '$id' ORDER BY m.TIMESTAMP ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                ?>
                    <div class="message-container-1by1 <?= ($row['MESS_ID'] == $row['SENDER_ID']) ? 'left-message' : 'right-message' ?>">
                        <article><?= $row['FIRST_NAME'] . ' ' . $row['MIDDLE_INITIAL'] . ' ' . $row['LAST_NAME'] ?> <br> <?= date("F j, Y g:i A", strtotime($row['TIMESTAMP'])) ?></article>
                        <p><?= $row['MESSAGE_BODY'] ?></p>
                    </div>
<?php
                }
            }
        }
    } elseif (isset($_POST['data'])) {
        if ($_POST['data'] === 'sendMessage') {
            $message = $_POST['message'];
            $messageId = $_POST['messageId'];
            $senderId = $_POST['senderId'];
            $sql = "INSERT INTO `message`(`MESS_ID`, `SENDER_ID`, `MESSAGE_BODY`, `TIMESTAMP`) VALUES ('$messageId','$senderId','$message','$currentDateTime')";
            if ($conn->query($sql)) {
                echo 'Sent';
            } else {
                echo 'Sending Failed';
            }
        }
    } else {
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
}
