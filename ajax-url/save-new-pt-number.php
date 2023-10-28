<?php
if (isset($_POST['id'], $_POST['number'])) {
    include('../database/db.php');

    $id = $_POST['id'];
    $number = $_POST['number'];

    $sql = "UPDATE `payment_type` SET `BANK_NUMBER`='$number' WHERE `TYPE_ID` = '$id'";

    if ($conn->query($sql)) {
        echo '200';
    } else {
        echo '400';
    }
} elseif (isset($_POST['action'], $_POST['id'])) {
    include('../database/db.php');
    $id = $_POST['id'];
    $action = $_POST['action'];

    $sql = "UPDATE `payment_type` SET `STATUS`='$action' WHERE `TYPE_ID` = '$id'";

    if ($conn->query($sql)) {
        echo '200';
    } else {
        echo $id;
        echo $action;
    }
}
