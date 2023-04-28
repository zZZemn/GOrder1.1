<?php
    include '../database/db.php';
    include '../time-date.php';

    $timestamp = $currentDateTime;

    // Get the form data
    $sender_id = $_POST['sender_id'];
    $mess_id = $_POST['message_id'];
    $message = $_POST['message'];

    $send_message = "INSERT INTO `message`(`MESS_ID`, `SENDER_ID`, `MESSAGE_BODY`, `TIMESTAMP`) 
                    VALUES ('$mess_id','$sender_id','$message','$timestamp')";
    
    if($conn->query($send_message))
    {
        $update_timestamp = "UPDATE `messages` SET `LATEST_MESS_TIMESTAMP`='$timestamp' WHERE MESS_ID = $mess_id";
        if($conn->query($update_timestamp))
        {
            echo 'Message Sent!';
        }
        else
        {
            echo 'Message not sent!';
        }
    }
    else
    {
        echo 'Message not sent!';
    }
?>
