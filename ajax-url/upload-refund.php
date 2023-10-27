<?php
if (isset($_POST['transaction_id'], $_FILES['refundPhoto'])) {
    include('../database/db.php');

    $orderId = $_POST['transaction_id'];

    if (!empty($_FILES['refundPhoto']['size'])) {
        $file_name = $_FILES['refundPhoto']['name'];
        $file_tmp = $_FILES['refundPhoto']['tmp_name'];
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);

        if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {

            $new_file_name = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 13) . '.' . $extension;
            $checkFileName = $conn->query("SELECT * FROM `order` WHERE `REFUND_PHOTO` = '$new_file_name'");
            while ($checkFileName->num_rows > 0) {
                $new_file_name = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 13) . '.' . $extension;
                $checkFileName = $conn->query("SELECT * FROM `order` WHERE `REFUND_PHOTO` = '$new_file_name'");
            }

            $destination = "../img/refunds/" . $new_file_name;
            if (file_exists($destination)) {
                // If it exists, delete it
                if (unlink($destination)) {
                    // File deleted successfully
                    if (move_uploaded_file($file_tmp, $destination)) {
                        $update_sql = "UPDATE `order` SET `REFUND_PHOTO` = '$new_file_name' WHERE `TRANSACTION_ID` = '$orderId'";
                    } else {
                        echo '404';
                    }
                } else {
                    echo '404';
                }
            } else {
                // If it doesn't exist, simply move the uploaded file
                if (move_uploaded_file($file_tmp, $destination)) {
                    $update_sql = "UPDATE `order` SET `REFUND_PHOTO` = '$new_file_name' WHERE `TRANSACTION_ID` = '$orderId'";
                } else {
                    echo '404';
                }
            }

            if ($conn->query($update_sql)) {
                echo '200';
            } else {
                echo 'Something Went Wrong!';
            }
        } else {
            echo 'File Extension Not Accepted';
        }
    } else {
        echo 'Please Upload valid ID';
    }
}
