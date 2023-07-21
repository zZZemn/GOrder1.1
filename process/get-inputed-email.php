<?php
if (isset($_POST['email']) && isset($_POST['acc_type'])) {
    include('../database/db.php');

    $acc_type = $_POST['acc_type'];
    $email = $_POST['email'];

    if ($acc_type === 'emp') {
        $email_sql = "SELECT EMP_ID, EMP_TYPE, FIRST_NAME, LAST_NAME, MIDDLE_INITIAL, SUFFIX FROM employee WHERE EMAIL = '$email'";
    } else {
        $email_sql = "SELECT CUST_ID, FIRST_NAME, LAST_NAME, MIDDLE_INITIAL, SUFFIX FROM customer_user WHERE EMAIL = '$email'";
    }

    if (($email_result = $conn->query($email_sql)) !== FALSE) {
        if ($email_result->num_rows > 0) {
            $result = $email_result->fetch_assoc();
            if ($acc_type === 'emp') {
                $emp_id = $result['EMP_ID'];
                $emp_type = $result['EMP_TYPE'];
                $name = $result['FIRST_NAME'] . ' ' . $result['MIDDLE_INITIAL'] . ' ' . $result['LAST_NAME'] . ' ' . $result['SUFFIX'];
                $response = [$emp_type, $emp_id, $name];
            } else {
                $cust_id = $result['CUST_ID'];
                $name = $result['FIRST_NAME'] . ' ' . $result['MIDDLE_INITIAL'] . ' ' . $result['LAST_NAME'] . ' ' . $result['SUFFIX'];
                $response = [$cust_id, $name, $acc_type];
            }

            echo json_encode($response);
        } else {
            echo 'not asd';
        }
    } else {
        echo 'not';
    }
} else {
    header('Location: ../index.php');
    exit;
}
