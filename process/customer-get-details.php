<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['cust_id'])) {
            $cust_id = $_POST['cust_id'];
            $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
            if ($cust_result = $conn->query($cust_sql)) {
                if ($cust_result->num_rows > 0) {
                    $cust = $cust_result->fetch_assoc();
                    $barangay_id = $cust['BARANGAY_ID'];
                                        
                    $bgy_sql = "SELECT MUNICIPALITY_ID, BARANGAY FROM barangay WHERE BARANGAY_ID = '$barangay_id'";
                    $bgy_result = $conn->query($bgy_sql);
                    $bgy = $bgy_result->fetch_assoc();

                    $bgy_name = $bgy['BARANGAY'];
                    $muni_id = $bgy['MUNICIPALITY_ID'];

                    $muni_sql = "SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'";
                    $muni_result = $conn->query($muni_sql);
                    $muni = $muni_result->fetch_assoc();

                    $muni_name = $muni['MUNICIPALITY'];
                    $prov_id = $muni['PROVINCE_ID'];

                    $prov_sql = "SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$prov_id'";
                    $prov_result = $conn->query($prov_sql);
                    $prov = $prov_result->fetch_assoc();

                    $region_id = $prov['REGION_ID'];
                    $prov_name = $prov['PROVINCE'];

                    $reg_sql = "SELECT REGION FROM region WHERE REGION_ID = '$region_id'";
                    $reg_result = $conn->query($reg_sql);
                    $reg = $reg_result->fetch_assoc();

                    $reg_name = $reg['REGION'];

                    $cust_details = [
                        'cust_id' => $cust['CUST_ID'],
                        'first_name' => $cust['FIRST_NAME'],
                        'last_name' => $cust['LAST_NAME'], 
                        'middle_initial' => $cust['MIDDLE_INITIAL'],
                        'suffix' => $cust['SUFFIX'],
                        'sex' => $cust['SEX'],
                        'email' => $cust['EMAIL'],
                        'username' => $cust['USERNAME'],
                        'contact_no' => $cust['CONTACT_NO'],
                        'unit_st' => $cust['UNIT_STREET'],
                        'barangay_id' => $cust['BARANGAY_ID'],
                        'picture' => $cust['PICTURE'],
                        'bday' => $cust['BIRTHDAY'],
                        'discount_type' => $cust['DISCOUNT_TYPE'],
                        'id_pic' => $cust['ID_PICTURE'],
                        'status' => $cust['STATUS'],
                        'address' => [
                            'region' => $reg_name,
                            'region_id' => $region_id,
                            'province' => $prov_name,
                            'province_id' => $prov_id,
                            'municipality' => $muni_name,
                            'municipality_id' => $muni_id,
                            'barangay' => $bgy_name,
                            'barangay_id' => $barangay_id
                        ]
                    ];

                    echo json_encode($cust_details);
                } else {
                    echo 'not';
                }
            } else {
                echo 'not';
            }
        } else {
            echo "
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>";
        }
    } else {
        echo "
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
