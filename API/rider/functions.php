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

function checkRider($id)
{
    global $conn;
    $sql = "SELECT * FROM `employee` WHERE `EMP_ID` = '$id' AND `EMP_TYPE` = 'Rider' AND `EMP_STATUS` = 'active'";
    return $conn->query($sql);
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
                        insertLog($emp_id, 'Log In');
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

function toDeliver($id, $status)
{
    global $conn;

    $rider_sql = "SELECT * FROM employee WHERE EMP_ID = '$id'";
    $rider_result = $conn->query($rider_sql);
    if ($rider_result->num_rows > 0) {
        $rider = $rider_result->fetch_assoc();
        if ($rider['EMP_TYPE'] === 'Rider') {

            $order_sql = "SELECT * FROM `order` WHERE RIDER_ID = '$id' AND DELIVERY_TYPE = 'Deliver' AND STATUS = '$status'";
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

function scanQR($transaction_id, $rider_id)
{
    global $currentDate;
    global $currentTime;
    global $conn;
    $orders_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id' AND RIDER_ID = '$rider_id'";
    $sales_sql = "SELECT * FROM `sales` WHERE ORDER_ID = '$transaction_id'";

    if (($orders_result = $conn->query($orders_sql)) !== false && ($sales_result = $conn->query($sales_sql)) !== false) {
        if ($orders_result->num_rows > 0 && $sales_result->num_rows > 0) {
            $orders = $orders_result->fetch_assoc();
            $sales = $sales_result->fetch_assoc();

            $order_update_sql = "UPDATE `order` SET `PAYMENT`= TOTAL, `STATUS`='Delivered' WHERE TRANSACTION_ID = '$transaction_id'";
            $sales_update_sql = "UPDATE `sales` SET `TIME`='$currentTime',`DATE`='$currentDate', `PAYMENT`= TOTAL WHERE ORDER_ID = '$transaction_id'";

            if ($conn->query($order_update_sql) && ($conn->query($sales_update_sql) === TRUE)) {
                $data = [
                    'status' => 200,
                    'message' => 'Transaction Completed',
                ];
                header("HTTP/1.0 404 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 200,
                    'message' => 'Transaction Not Complete',
                ];
                header("HTTP/1.0 404 OK");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 404,
                'message' => 'Access Denied 1',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 404,
            'message' => 'Access Denied 2',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}

function rider($id)
{
    global $conn;

    $rider_sql = "SELECT * FROM employee WHERE EMP_TYPE = 'Rider' AND EMP_ID = '$id' AND EMP_STATUS = 'active'";
    $rider_result = $conn->query($rider_sql);
    if ($rider_result->num_rows > 0) {
        $rider = $rider_result->fetch_assoc();
        $data = [
            'status' => 200,
            'message' => 'Rider Fetch Successfully',
            'data' => [
                'emp_id' => $rider['EMP_ID'],
                'name' => $rider['FIRST_NAME'] . ' ' . $rider['MIDDLE_INITIAL'] . ' ' . $rider['LAST_NAME'] . ' ' . $rider['SUFFIX'],
                'email' => $rider['EMAIL'],
                'username' => $rider['USERNAME'],
                'contact_no' => $rider['CONTACT_NO'],
                'address' => $rider['ADDRESS'],
                'birthday' => $rider['BIRTHDAY'],
                'picture' => 'https://gorder.website/img/userprofile/' . $rider['PICTURE']
            ]
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        $message = 'No Rider Found';
        return error422($message);
    }
}

function returns($id)
{
    global $conn;
    $checkRider = checkRider($id);
    if ($checkRider->num_rows > 0) {
        $retSql = "SELECT * FROM `return` WHERE `RIDER_ID` = '$id' AND `STATUS` = 'Pending'";
        $retResult = $conn->query($retSql);
        if ($retResult->num_rows > 0) {
            $returns = [];
            while ($ret = $retResult->fetch_assoc()) {
                $retRow = [
                    "return_id" => $ret['RETURN_ID'],
                    "transaction_id" => $ret['TRANSACTION_ID'],
                    "return_reason" => $ret['RETURN_REASON'],
                    "return_amount" => $ret['RETURN_AMOUNT'],
                    "return_date" => $ret['RETURN_DATE']
                ];
                $returns[] = $retRow;
            }

            $data = [
                'status' => 200,
                'message' => 'All return requests Fetch Successfully',
                'data' => $returns
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 200,
                'message' => 'No returns found.'
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }
    } else {
        return error422("Invalid Rider ID");
    }
}

error_reporting(1);

function returnDetails($id, $returnID)
{
    global $conn;
    $checkRider = checkRider($id);
    if ($checkRider->num_rows > 0) {
        $retSql = "SELECT r.*, s.*, c.*, bgy.BARANGAY, muni.MUNICIPALITY, prov.PROVINCE, reg.REGION
                   FROM `return` AS r
                   JOIN `sales` AS s ON r.TRANSACTION_ID = s.TRANSACTION_ID
                   JOIN `customer_user` AS c ON s.CUST_ID = c.CUST_ID
                   JOIN `barangay` AS bgy ON c.BARANGAY_ID = bgy.BARANGAY_ID
                   JOIN `municipality` AS muni ON bgy.MUNICIPALITY_ID = muni.MUNICIPALITY_ID
                   JOIN `province` AS prov ON muni.PROVINCE_ID = prov.PROVINCE_ID
                   JOIN `region` AS reg ON prov.REGION_ID = reg.REGION_ID
                   WHERE r.RETURN_ID = '$returnID' AND r.RIDER_ID = '$id' 
                   AND r.STATUS = 'Pending'";

        $retResult = $conn->query($retSql);
        if ($retResult->num_rows > 0) {
            $returnDetails = $retResult->fetch_assoc();

            $returnItemsSQL = "SELECT ri.*, inv.*, p.* 
                               FROM `return_items` AS ri
                               JOIN `inventory` AS inv ON ri.INV_ID = inv.INV_ID
                               JOIN `products` AS p ON inv.PRODUCT_ID = p.PRODUCT_ID
                               WHERE ri.RETURN_ID = '$returnID'";

            $returnItemsResult = $conn->query($returnItemsSQL);
            if ($returnItemsResult->num_rows > 0) {
                $returnItems = [];
                while ($returnItemsDetailsRow = $returnItemsResult->fetch_assoc()) {
                    $item = [
                        "product_name" => $returnItemsDetailsRow['PRODUCT_NAME'],
                        "MG" => $returnItemsDetailsRow['MG'],
                        "G" => $returnItemsDetailsRow['G'],
                        "ML" => $returnItemsDetailsRow['ML'],
                        "product_img" => $returnItemsDetailsRow['PRODUCT_IMG'],
                        "expiration_date" => $returnItemsDetailsRow['EXP_DATE'],
                        "qty" => $returnItemsDetailsRow['QTY']
                    ];
                    $returnItems[] = $item;
                }

                $data = [
                    'status' => 200,
                    'message' => 'Return Fetch Successfully',
                    'return_details' => [
                        "return_id" => $returnDetails['RETURN_ID'],
                        "return_reason" => $returnDetails['RETURN_REASON'],
                        "return_amount" => $returnDetails['RETURN_AMOUNT'],
                        "return_date" => $returnDetails['RETURN_DATE'],
                        "name" => $returnDetails['FIRST_NAME'] . ' ' . $returnDetails['MIDDLE_INITIAL'] . ' ' . $returnDetails['LAST_NAME'],
                        "address" => $returnDetails['UNIT_STREET'] . ', ' . $returnDetails['BARANGAY'] . ', ' . $returnDetails['MUNICIPALITY'] . ', ' . $returnDetails['PROVINCE'] . ', ' . $returnDetails['REGION'],
                        "contact_no" => $returnDetails['CONTACT_NO']
                    ],
                    'items' => $returnItems
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            } else {
                return error422("Something went wrong!");
            }
        } else {
            return error422("Invalid Return ID");
        }
    } else {
        return error422("Invalid Rider ID");
    }
}

function acceptReturn($riderID, $returnID)
{
    global $conn;

    $checkRider = checkRider($riderID);
    if ($checkRider->num_rows > 0) {
        $return_sql = $conn->query("SELECT r.TRANSACTION_ID, r.RETURN_AMOUNT, s.CUST_ID
                                    FROM `return` AS r
                                    JOIN `sales` AS s ON r.TRANSACTION_ID = s.TRANSACTION_ID
                                    WHERE r.RETURN_ID = '$returnID' AND r.RIDER_ID = '$riderID' AND r.STATUS = 'Pending'");
        if ($return_sql->num_rows > 0) {
            $return_result = $return_sql->fetch_assoc();
            $custID = $return_result['CUST_ID'];
            $transactionID = $return_result['TRANSACTION_ID'];
            $returnAmount = $return_result['RETURN_AMOUNT'];

            $updateReturnStatus = "UPDATE `return` SET `STATUS` = 'Done' WHERE `RETURN_ID` = '$returnID'";
            $updateSalesNewTotal = "UPDATE `sales` SET `UPDATED_TOTAL` = UPDATED_TOTAL - '$returnAmount' WHERE `TRANSACTION_ID` = '$transactionID'";
            $addVoucher = "UPDATE `customer_user` SET `VOUCHER` = VOUCHER + '$returnAmount' WHERE `CUST_ID` = '$custID'";

            if ($conn->query($updateReturnStatus) === TRUE && $conn->query($updateSalesNewTotal) === TRUE && $conn->query($addVoucher) === TRUE) {
                $data = [
                    'status' => 200,
                    'message' => 'Return Transaction Completed.'
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            } else {
                return error422("Something Went Wrong");
            }
        } else {
            return error422("Invalid Request");
        }
    } else {
        return error422("Invalid Rider ID");
    }
}
