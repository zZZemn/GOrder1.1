<?php
require '../../database/db.php';
require '../../time-date.php';

function error422($message)
{
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    return json_encode($data);
}

function login($email, $password)
{
    global $conn;
    global $currentDate;
    global $currentTime;

    if ($email && $password == NULL) {
        return error422('Enter email and password');
    } else {
        $sql = "SELECT * FROM employee WHERE EMAIL = '$email' OR USERNAME = '$email'";
        $result =  $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['PASSWORD'])) {
                if ($user['EMP_STATUS'] === 'active' && $user['EMP_TYPE'] === 'Admin' || $user['EMP_TYPE'] === 'Rider') {
                    $emp_id = $user['EMP_ID'];
                    $login_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) 
                                            VALUES ('$emp_id','Login (Rider App)','$currentDate','$currentTime')";
                    if ($conn->query($login_log) === TRUE) {
                        $data = [
                            'status' => 200,
                            'message' => 'Login Success',
                            'emp_type' => $user['EMP_TYPE'],
                            'data' => $emp_id,
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);
                    } else {
                        $data = [
                            'status' => 404,
                            'message' => 'Login Failed',
                        ];
                        header("HTTP/1.0 200 Something Wrong");
                        return json_encode($data);
                    }
                } else {
                    $data = [
                        'status' => 404,
                        'message' => 'Login Failed',
                    ];
                    header("HTTP/1.0 404 Not Found");
                    return json_encode($data);
                }
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'Login Failed',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 404,
                'message' => 'Login Failed',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }
}

function toDeliver($id)
{
    global $conn;

    $rider_sql = "SELECT * FROM employee WHERE EMP_ID = '$id'";
    $rider_result = $conn->query($rider_sql);
    if ($rider_result->num_rows > 0) {
        $rider = $rider_result->fetch_assoc();
        if ($rider['EMP_TYPE'] === 'Rider') {

            $order_sql = "SELECT * FROM `order` WHERE RIDER_ID = '$id' AND DELIVERY_TYPE = 'Deliver' AND (STATUS = 'For-Delivery' OR STATUS = 'Shipped')";
            $order_result = $conn->query($order_sql);
            if ($order_result->num_rows > 0) {
                $orderlist = [];
                while ($order = $order_result->fetch_assoc()) {
                    $transaction_id = $order['TRANSACTION_ID'];
                    $cust_id = $order['CUST_ID'];
                    $user_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'");
                    $user = $user_result->fetch_assoc();
                    $fullname = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];

                    $bgy_id = $order['BARANGAY_ID'];

                    $barangay_result = $conn->query("SELECT MUNICIPALITY_ID, BARANGAY, DELIVERY_FEE FROM barangay WHERE BARANGAY_ID = '$bgy_id'");
                    $barangay = $barangay_result->fetch_assoc();

                    $muni_id = $barangay['MUNICIPALITY_ID'];
                    $barangay_name = $barangay['BARANGAY'];
                    $df = $barangay['DELIVERY_FEE'];

                    $municipality_result = $conn->query("SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'");
                    $municipality = $municipality_result->fetch_assoc();

                    $province_id = $municipality['PROVINCE_ID'];
                    $municipality_name = $municipality['MUNICIPALITY'];

                    $province_result = $conn->query("SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$province_id'");
                    $province = $province_result->fetch_assoc();

                    $region_id = $province['REGION_ID'];
                    $province_name = $province['PROVINCE'];

                    $region_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
                    $region = $region_result->fetch_assoc();

                    $region_name = $region['REGION'];

                    $full_address = $order['UNIT_STREET'] . ', ' . $barangay_name . ', ' . $municipality_name . ', ' . $province_name . ', ' . $region_name;

                    $orders = [
                        'transaction_id' => $transaction_id,
                        'order_status' => $order['STATUS'],
                        'cust_id' => $cust_id,
                        'cust_name' => $fullname,
                        'payment_type' => $order['PAYMENT_TYPE'],
                        'address' => $full_address,
                        'total' => floatval($order['TOTAL']),
                    ];

                    $orderlist[] = $orders;
                }

                $data = [
                    'status' => 200,
                    'message' => 'All orders that you need to deliver asap',
                    'orders' => $orderlist
                ];
                header("HTTP/1.0 404 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 200,
                    'message' => 'No Order',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 404,
                'message' => 'Not Rider',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 404,
            'message' => 'No Emp Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}


function deliverDetails($rider_id, $order_id)
{
    global $conn;
    $order_result = $conn->query("SELECT * FROM `order` WHERE TRANSACTION_ID = '$order_id' AND RIDER_ID = '$rider_id' AND (STATUS = 'For-Delivery' OR STATUS = 'Shipped')");
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        $cust_id = $order['CUST_ID'];

        $user_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'");
        $user = $user_result->fetch_assoc();
        $fullname = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];

        $bgy_id = $order['BARANGAY_ID'];

        $barangay_result = $conn->query("SELECT MUNICIPALITY_ID, BARANGAY, DELIVERY_FEE FROM barangay WHERE BARANGAY_ID = '$bgy_id'");
        $barangay = $barangay_result->fetch_assoc();

        $muni_id = $barangay['MUNICIPALITY_ID'];
        $barangay_name = $barangay['BARANGAY'];
        $df = $barangay['DELIVERY_FEE'];

        $municipality_result = $conn->query("SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'");
        $municipality = $municipality_result->fetch_assoc();

        $province_id = $municipality['PROVINCE_ID'];
        $municipality_name = $municipality['MUNICIPALITY'];

        $province_result = $conn->query("SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$province_id'");
        $province = $province_result->fetch_assoc();

        $region_id = $province['REGION_ID'];
        $province_name = $province['PROVINCE'];

        $region_result = $conn->query("SELECT REGION FROM region WHERE REGION_ID = '$region_id'");
        $region = $region_result->fetch_assoc();

        $region_name = $region['REGION'];

        $full_address = $order['UNIT_STREET'] . ', ' . $barangay_name . ', ' . $municipality_name . ', ' . $province_name . ', ' . $region_name;

        $order_details_result = $conn->query("SELECT * FROM order_details WHERE TRANSACTION_ID = '$order_id'");
        $order_details_array = [];
        if ($order_details_result->num_rows > 0) {
            while ($order_details = $order_details_result->fetch_assoc()) {
                $product_id = $order_details['PRODUCT_ID'];
                $product_result = $conn->query("SELECT PRODUCT_NAME, PRODUCT_IMG FROM products WHERE PRODUCT_ID = $product_id");
                $product = $product_result->fetch_assoc();
                $product_name = $product['PRODUCT_NAME'];
                $product_img = 'https://gorder.website/img/products/' . $product['PRODUCT_IMG'];

                $order_details_1 = [
                    'product_name' => $product_name,
                    'product_img' => $product_img,
                    'qty' => intval($order_details['QTY']),
                    'amount' => floatval($order_details['AMOUNT'])
                ];
                $order_details_array[] = $order_details_1;
            }
            $orders = [
                'transaction_id' => $order_id,
                'order_status' => $order['STATUS'],
                'cust_id' => $cust_id,
                'cust_name' => $fullname,
                'payment_type' => $order['PAYMENT_TYPE'],
                'address' => $full_address,
                'total' => floatval($order['TOTAL']),
                'products' => $order_details_array
            ];

            $data = [
                'status' => 200,
                'message' => 'Order Fetch Success',
                'order_details' => $orders
            ];
            header("HTTP/1.0 404 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 200,
                'message' => 'Order Empty'
            ];
            header("HTTP/1.0 404 OK");
            return json_encode($data);
            exit;
        }
    } else {
        $data = [
            'status' => 404,
            'message' => 'Access Denied',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}
