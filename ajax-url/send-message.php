<?php
include '../database/db.php';
include '../time-date.php';

$timestamp = $currentDateTime;
$sender_id = $_POST['sender_id'];
$mess_id = $_POST['message_id'];
$message = $_POST['message'];

$send_message = "INSERT INTO `message`(`MESS_ID`, `SENDER_ID`, `MESSAGE_BODY`, `TIMESTAMP`) VALUES (?,?,?,?)";
$stmt = $conn->prepare($send_message);

if ($stmt) {
    $stmt->bind_param("ssss", $mess_id, $sender_id, $message, $timestamp); // Bind parameters

    if ($stmt->execute()) {
        $update_timestamp = "UPDATE `messages` SET `LATEST_MESS_TIMESTAMP`='$timestamp' WHERE MESS_ID = $mess_id";
        if ($conn->query($update_timestamp)) {
            echo 'Message Sent!';
        } else {
            echo 'Message not sent!';
        }
    } else {
        echo 'Message not sent!';
    }

    $stmt->close(); // Close the prepared statement
} else {
    echo 'Message not sent!';
}
