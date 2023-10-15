<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['serializedData']) && isset($_POST['buttonType'])) {
        $serializedData = $_POST['serializedData'];
        $buttonType = $_POST['buttonType'];

        parse_str($serializedData, $formData);

        $onek = $formData["onek"];
        $fiveHundred = $formData["fiveHundred"];
        $twoHundred = $formData["twoHundred"];
        $oneHundred = $formData["oneHundred"];
        $fifty = $formData["fifty"];
        $twenty = $formData["twenty"];
        $ten = $formData["ten"];
        $five = $formData["five"];
        $one = $formData["one"];
        $cents25 = $formData["cents25"];
        $emp_id = $emp['EMP_ID'];
        $timeDate = $currentDateTime;
        $type = $buttonType;

        $insert = "INSERT INTO `rellero`(`EMP_ID`, `DATE_TIME`, `ONE_THOUSAND`, `FIVE_HUNDRED`, `TWO_HUNDRED`, `ONE_HUNDRED`, `FIFTY`, `TWENTY`, `TEN`, `FIVE`, `ONE`, `TWENTY_FIVE_CENTS`, `TYPE`) VALUES 
                                        ('$emp_id','$timeDate','$onek','$fiveHundred','$twoHundred','$oneHundred','$fifty','$twenty','$ten','$five','$one','$cents25','$type')";

        if ($conn->query($insert)) {
            echo 'insert-money-success';
        } else {
            echo 'insert-money-error';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
