<?php

require '../database/db.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../time-date.php';

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

    if ($email && $password == NULL) {
        return error422('Enter email and password');
    } else {
        $sql = "SELECT * FROM customer_user WHERE EMAIL = '$email' OR USERNAME = '$email'";
        $result =  $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['PASSWORD'])) {
                if ($user['STATUS'] === 'active') {
                    $data = [
                        'status' => 200,
                        'message' => 'Login Success',
                        'data' => $user['CUST_ID'],
                    ];
                    header("HTTP/1.0 200 OK");
                    return json_encode($data);
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

function signup($fname, $lname, $mi, $suffix, $sex, $email, $username, $password, $contact_no, $unit_street, $barangay, $municipality, $province, $region, $birthday)
{

    global $conn;

    if ($fname == null || $lname == null) {
        return error422($fname . " " . $lname);
    } elseif ($email == null) {
        return error422('Please Enter your Email');
    } elseif ($username == null) {
        return error422('Please Enter Username');
    } elseif ($password == null) {
        return error422('Please Enter Password');
    } elseif ($contact_no == null) {
        return error422('Please Enter you Contact number');
    } elseif ($contact_no == null) {
        return error422('Please Enter you Contact number');
    } elseif ($unit_street == null || $barangay == null || $municipality == null || $province == null || $region == null) {
        return error422('Please Enter your full address');
    } elseif ($birthday == null) {
        return error422('Please Enter your Birthdate');
    } else {
        $check_email = "SELECT * FROM customer_user WHERE EMAIL = '$email'";
        $check_email_result =  $conn->query($check_email);

        $check_username = "SELECT * FROM customer_user WHERE USERNAME = '$username'";
        $check_username_result = $conn->query($check_username);

        if ($check_email_result->num_rows > 0) {
            $data = [
                'status' => 404,
                'message' => 'Existing email',
            ];
            header("HTTP/1.0 404 Existing email");
            return json_encode($data);
        } elseif ($check_username_result->num_rows > 0) {
            $data = [
                'status' => 404,
                'message' => 'Existing username',
            ];
            header("HTTP/1.0 404 Existing username");
            return json_encode($data);
        } else {
            $verification_code = rand(100000, 999999);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $cust_id = rand(100000, 999999);
            $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");

            while ($cust_id_result->num_rows > 0) {
                $cust_id = rand(100000, 999999);
                $cust_id_result = $conn->query("SELECT * FROM customer_user WHERE CUST_ID = $cust_id");
            }

            $insert_customer = "INSERT INTO `customer_user`(`CUST_ID`, `FIRST_NAME`, `LAST_NAME`, `MIDDLE_INITIAL`, `SUFFIX`, `SEX`, `EMAIL`, `USERNAME`, `PASSWORD`, `CONTACT_NO`, `UNIT_STREET`, `BARANGAY`, `MUNICIPALITY`, `PROVINCE`, `REGION`, `BIRTHDAY`, `CUSTOMER_TYPE`, `STATUS`) 
                                VALUES ('$cust_id','$fname','$lname','$mi','$suffix','$sex','$email','$username','$hashed_password','$contact_no','$unit_street','$barangay','$municipality','$province','$region','$birthday','regular','active')";

            if ($conn->query($insert_customer)) {
                $data = [
                    'status' => 200,
                    'message' => 'Registered!'
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 200,
                    'message' => 'Registration failed!'
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            }
        }
    }
}

function products($cust_id_search)
{
    global $conn;

    if ($cust_id_search['id'] == null) {
        return error422('Enter Customer ID');
    } else {
        $id = $cust_id_search['id'];

        $cheking_cust_id = "SELECT * FROM customer_user WHERE CUST_ID = $id";
        $cheking_cust_id_result = $conn->query($cheking_cust_id);

        if ($cheking_cust_id_result->num_rows > 0) {
            if (isset($cust_id_search['pro_search'])) {
                if ($cust_id_search['pro_search'] != null) {
                    //search here
                    $pro_search = $cust_id_search['pro_search'];
                    $product_search = "SELECT * FROM products WHERE PRODUCT_NAME LIKE '%$pro_search%'";
                    $product_search_result = $conn->query($product_search);

                    $product_search_data = [];

                    if ($product_search_result->num_rows > 0) {
                        while ($row = $product_search_result->fetch_assoc()) {
                            $pro_id = $row['PRODUCT_ID'];
                            $inv_sql = "SELECT * FROM inventory WHERE PRODUCT_ID = '$pro_id'";
                            $inv_result = $conn->query($inv_sql);
                            $qty_left = 0;
                            if ($inv_result->num_rows > 0) {
                                while ($inv = $inv_result->fetch_assoc()) {
                                    $qty_left += $inv['QUANTITY'];
                                }
                            } else {
                                $qty_left = 0;
                            }
                            $product_search_data[] = [
                                'product_id' => $row['PRODUCT_ID'],
                                'product_code' => $row['PRODUCT_CODE'],
                                'product_name' => $row['PRODUCT_NAME'],
                                'unit_measurement' => $row['UNIT_MEASUREMENT'],
                                'selling_price' => $row['SELLING_PRICE'],
                                'subcat_id' => $row['SUB_CAT_ID'],
                                'description' => $row['DESCRIPTION'],
                                'critical_level' => $row['CRITICAL_LEVEL'],
                                'product_img' => 'https://gorder.website/img/products/' . $row['PRODUCT_IMG'],
                                'prescribe' => $row['PRESCRIBE'],
                                'vatable' => $row['VATABLE'],
                                'quantity' => $qty_left
                            ];
                        }
                        $data = [
                            'status' => 200,
                            'message' => 'Product Fetch Success',
                            'data' => $product_search_data
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);
                    } else {
                        $data = [
                            'status' => 404,
                            'message' => 'No product found',
                        ];
                        header("HTTP/1.0 404 No product found");
                        return json_encode($data);
                    }
                } else {
                    $products = "SELECT * FROM products";
                    $products_result = $conn->query($products);

                    $products_data = [];

                    if ($products_result->num_rows > 0) {
                        while ($row = $products_result->fetch_assoc()) {
                            $pro_id = $row['PRODUCT_ID'];
                            $inv_sql = "SELECT * FROM inventory WHERE PRODUCT_ID = '$pro_id'";
                            $inv_result = $conn->query($inv_sql);
                            $qty_left = 0;
                            if ($inv_result->num_rows > 0) {
                                while ($inv = $inv_result->fetch_assoc()) {
                                    $qty_left += $inv['QUANTITY'];
                                }
                            } else {
                                $qty_left = 0;
                            }
                            $products_data[] = [
                                'product_id' => $row['PRODUCT_ID'],
                                'product_code' => $row['PRODUCT_CODE'],
                                'product_name' => $row['PRODUCT_NAME'],
                                'unit_measurement' => $row['UNIT_MEASUREMENT'],
                                'selling_price' => $row['SELLING_PRICE'],
                                'subcat_id' => $row['SUB_CAT_ID'],
                                'description' => $row['DESCRIPTION'],
                                'critical_level' => $row['CRITICAL_LEVEL'],
                                'product_img' => 'https://gorder.website/img/products/' . $row['PRODUCT_IMG'],
                                'prescribe' => $row['PRESCRIBE'],
                                'vatable' => $row['VATABLE'],
                                'quantity' => $qty_left
                            ];
                        }

                        $data = [
                            'status' => 200,
                            'message' => 'Product Fetch Success',
                            'data' => $products_data
                        ];
                        header("HTTP/1.0 200 OK");
                        return json_encode($data);
                    } else {
                        $data = [
                            'status' => 404,
                            'message' => 'No product found',
                        ];
                        header("HTTP/1.0 404 No product found");
                        return json_encode($data);
                    }
                }
            } else {
                $products = "SELECT * FROM products";
                $products_result = $conn->query($products);

                $products_data = [];

                if ($products_result->num_rows > 0) {
                    while ($row = $products_result->fetch_assoc()) {
                        $pro_id = $row['PRODUCT_ID'];
                        $inv_sql = "SELECT * FROM inventory WHERE PRODUCT_ID = '$pro_id'";
                        $inv_result = $conn->query($inv_sql);
                        $qty_left = 0;
                        if ($inv_result->num_rows > 0) {
                            while ($inv = $inv_result->fetch_assoc()) {
                                $qty_left += $inv['QUANTITY'];
                            }
                        } else {
                            $qty_left = 0;
                        }
                        $products_data[] = [
                            'product_id' => $row['PRODUCT_ID'],
                            'product_code' => $row['PRODUCT_CODE'],
                            'product_name' => $row['PRODUCT_NAME'],
                            'unit_measurement' => $row['UNIT_MEASUREMENT'],
                            'selling_price' => $row['SELLING_PRICE'],
                            'subcat_id' => $row['SUB_CAT_ID'],
                            'description' => $row['DESCRIPTION'],
                            'critical_level' => $row['CRITICAL_LEVEL'],
                            'product_img' => 'https://gorder.website/img/products/' . $row['PRODUCT_IMG'],
                            'prescribe' => $row['PRESCRIBE'],
                            'vatable' => $row['VATABLE'],
                            'quantity' => $qty_left
                        ];
                    }

                    $data = [
                        'status' => 200,
                        'message' => 'Product Fetch Success',
                        'data' => $products_data
                    ];
                    header("HTTP/1.0 200 OK");
                    return json_encode($data);
                } else {
                    $data = [
                        'status' => 404,
                        'message' => 'No product found',
                    ];
                    header("HTTP/1.0 404 No product found");
                    return json_encode($data);
                }
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'Access Deny',
            ];
            header("HTTP/1.0 405 Access Deny");
            echo json_encode($data);
        }
    }
}

function addToCart($productID, $custID)
{
    global $conn;

    $check_cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = $custID";
    $check_cust_result = $conn->query($check_cust_sql);

    if ($check_cust_result->num_rows > 0) {
        $cust = $check_cust_result->fetch_assoc();

        $cartID = $cust['CART_ID'];

        $check_pro_exist_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$productID'";
        $check_pro_exist_result = $conn->query($check_pro_exist_sql);
        if ($check_pro_exist_result->num_rows > 0) {
            $product = $check_pro_exist_result->fetch_assoc();
            $product_selling_price = $product['SELLING_PRICE'];

            $check_product_exist_cart_sql = "SELECT * FROM cart_items WHERE PRODUCT_ID = '$productID' AND CART_ID = '$cartID'";
            $check_product_exist_cart_result = $conn->query($check_product_exist_cart_sql);
            if ($check_product_exist_cart_result->num_rows > 0) {
                $update_cart_qty = "UPDATE `cart_items`
                SET `QTY` = `QTY` + 1,
                    `AMOUNT` = `AMOUNT` + '$product_selling_price'
                WHERE CART_ID = '$cartID' AND PRODUCT_ID = '$productID'";
                if ($conn->query($update_cart_qty)) {
                    $data = [
                        'status' => 200,
                        'message' => 'Cart Updated',
                    ];
                    header("HTTP/1.0 405 Access Deny");
                    echo json_encode($data);
                } else {
                    $data = [
                        'status' => 405,
                        'message' => 'Wrong Query',
                    ];
                    header("HTTP/1.0 405 Access Deny");
                    echo json_encode($data);
                }
            } else {
                $amount = $product_selling_price * 1;

                $cart_insert = "INSERT INTO `cart_items`(`CART_ID`, `PRODUCT_ID`, `QTY`, `AMOUNT`) 
                                VALUES ('$cartID','$productID',1,'$amount')";

                if ($conn->query($cart_insert) === TRUE) {
                    $data = [
                        'status' => 200,
                        'message' => 'Added To Cart',
                    ];
                    header("HTTP/1.0 405 Access Deny");
                    echo json_encode($data);
                } else {
                    $data = [
                        'status' => 405,
                        'message' => 'Wrong Query',
                    ];
                    header("HTTP/1.0 405 Access Deny");
                    echo json_encode($data);
                }
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'No product found',
            ];
            header("HTTP/1.0 405 Access Deny");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No cust Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        echo json_encode($data);
    }
}

function minusToCart($productID, $custID)
{
    global $conn;

    $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$custID'";
    $cust_result = $conn->query($cust_sql);

    if ($cust_result->num_rows > 0) {
        $customer = $cust_result->fetch_assoc();

        $cartID = $customer['CART_ID'];

        $cart_sql = "SELECT * FROM cart_items WHERE CART_ID = '$cartID' AND PRODUCT_ID = '$productID'";
        $cart_result = $conn->query($cart_sql);
        if ($cart_result->num_rows > 0) {
            $cart = $cart_result->fetch_assoc();

            $cart_qty = $cart['QTY'];

            if ($cart_qty > 1) {
                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$productID'";
                $product_result = $conn->query($product_sql);
                if ($product_result->num_rows > 0) {
                    $product = $product_result->fetch_assoc();
                    $selling_price = $product['SELLING_PRICE'];
                    $cart_items_sql = "UPDATE `cart_items` SET `QTY`= QTY - 1,`AMOUNT`= AMOUNT - $selling_price WHERE 1";
                    if ($conn->query($cart_items_sql) === TRUE) {
                        $data = [
                            'status' => 405,
                            'message' => 'Cart Updated',
                        ];
                        header("HTTP/1.0 405 Access Deny");
                        return json_encode($data);
                    } else {
                        $data = [
                            'status' => 405,
                            'message' => 'Not success',
                        ];
                        header("HTTP/1.0 405 Access Deny");
                        return json_encode($data);
                    }
                } else {
                    $data = [
                        'status' => 405,
                        'message' => 'No Product Found',
                    ];
                    header("HTTP/1.0 405 Access Deny");
                    return json_encode($data);
                }
            } else {
                $data = [
                    'status' => 200,
                    'message' => 'Cart Qt Minimun is 1',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'No item found in cart',
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No cust Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}

function cartItems($custID)
{
    global $conn;

    $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$custID'";
    $cust_result = $conn->query($cust_sql);

    if ($cust_result->num_rows > 0) {
        $customer = $cust_result->fetch_assoc();

        $cartID = $customer['CART_ID'];

        $cart_sql = "SELECT * FROM cart_items WHERE CART_ID = '$cartID'";
        $cart_result = $conn->query($cart_sql);

        $total = 0;
        if ($cart_result->num_rows > 0) {
            $cart_items = [];

            while ($row = $cart_result->fetch_assoc()) {
                $product_id = $row['PRODUCT_ID'];
                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                $product_result = $conn->query($product_sql);
                $product = $product_result->fetch_assoc();

                $cart_items[] = [
                    'product_name' => $product['PRODUCT_NAME'],
                    'picture' => 'gorder.website/img/products/' . $product['PRODUCT_IMG'],
                    'selling_price' => floatval($product['SELLING_PRICE']),
                    'product_id' => $row['PRODUCT_ID'],
                    'qty' => intval($row['QTY']),
                    'amount' => floatval($row['AMOUNT']),
                ];

                $total += $row['AMOUNT'];
            }
            $data = [
                'status' => 200,
                'message' => 'Cart Items Fetch Success',
                'cust_id' => $custID,
                'cart_id' => $cartID,
                'total' => $total,
                'data' => $cart_items
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 200,
                'message' => 'Cart Is Empty',
                'cust_id' => $custID,
                'cart_id' => $cartID,
                'data' => 'Empty'
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No cust Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}

function deleteCart($cust_id, $product_id)
{
    global $conn;

    $cust_query = "SELECT CART_ID FROM customer_user WHERE CUST_ID = '$cust_id'";
    $cust_result = $conn->query($cust_query);
    if ($cust_result->num_rows > 0) {
        $cust = $cust_result->fetch_assoc();
        $cart_id = $cust['CART_ID'];

        $cart_sql = "DELETE FROM `cart_items` WHERE CART_ID = '$cart_id' AND PRODUCT_ID = '$product_id'";
        if ($conn->query($cart_sql) === TRUE) {
            $product_sql = "SELECT PRODUCT_NAME FROM products WHERE PRODUCT_ID = '$product_id'";
            $product_result = $conn->query($product_sql);
            if ($product_result->num_rows > 0) {
                $product = $product_result->fetch_assoc();
                $product_name = $product['PRODUCT_NAME'];

                $data = [
                    'status' => 200,
                    'message' => $product_name . ' Deleted',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 405,
                    'message' => 'No Product Found!',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 200,
                'message' => 'Product Not Removed from Cart',
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No cust Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}

function user($user_id)
{
    global $conn;
    if ($user_id['id'] == null) {
        return error422('Enter Customer ID');
    } else {
        $u_id = $user_id['id'];
        if (is_numeric($u_id)) {
            $userID = filter_var($user_id['id'], FILTER_SANITIZE_NUMBER_INT);
            $user_sql = "SELECT * FROM customer_user WHERE CUST_ID = $userID LIMIT 1";
            $user_result = $conn->query($user_sql);
            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                $bgy_id = $user['BARANGAY_ID'];

                $barangay_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                $barangay_result = $conn->query($barangay_sql);
                $barangay = $barangay_result->fetch_assoc();

                $barangay_name = $barangay['BARANGAY'];
                $municipality_id = $barangay['MUNICIPALITY_ID'];

                $municipality_sql = "SELECT * FROM municipality WHERE MUNICIPALITY_ID = '$municipality_id'";
                $municipality_result = $conn->query($municipality_sql);
                $municipality = $municipality_result->fetch_assoc();

                $municipality_name = $municipality['MUNICIPALITY'];
                $province_id = $municipality['PROVINCE_ID'];

                $province_sql = "SELECT * FROM province WHERE PROVINCE_ID = '$province_id'";
                $province_result = $conn->query($province_sql);
                $province = $province_result->fetch_assoc();

                $province_name = $province['PROVINCE'];
                $region_id = $province['REGION_ID'];

                $region_sql = "SELECT * FROM region WHERE REGION_ID = '$region_id'";
                $region_result = $conn->query($region_sql);
                $region = $region_result->fetch_assoc();

                $region_name = $region['REGION'];

                $data = [
                    'status' => 200,
                    'message' => 'User Found',
                    'data' => [
                        'first_name' => $user['FIRST_NAME'],
                        'last_name' => $user['LAST_NAME'],
                        'middle_initial' => $user['MIDDLE_INITIAL'],
                        'suffix' => $user['SUFFIX'],
                        'sex' => $user['SEX'],
                        'email' => $user['EMAIL'],
                        'username' => $user['USERNAME'],
                        'contact_no' => $user['CONTACT_NO'],
                        'unit_st' => $user['UNIT_STREET'],
                        'barangay_id' => $bgy_id,
                        'barangay' => $barangay_name,
                        'municipality' => $municipality_name,
                        'province' => $province_name,
                        'region' => $region_name,
                        'picture' => 'https://gorder.website/img/userprofile/' . $user['PICTURE'],
                        'bday' => $user['BIRTHDAY'],
                        'id_picture' => $user['ID_PICTURE']
                    ]
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 405,
                    'message' => 'No Customer Found',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'Access Deny',
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    }
}


function checkout($id)
{
    global $conn;

    $user_details_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$id'";
    $user_details_result = $conn->query($user_details_sql);
    if ($user_details_result->num_rows > 0) {
        $user = $user_details_result->fetch_assoc();
        if ($user['STATUS'] === 'active') {
            $bgy_id = $user['BARANGAY_ID'];
            $cart_id = $user['CART_ID'];
            $discount_type = $user['DISCOUNT_TYPE'];

            $order_items_sql = "SELECT * FROM cart_items WHERE CART_ID = '$cart_id'";
            $order_items_result = $conn->query($order_items_sql);
            $order_items_array = [];
            if ($order_items_result->num_rows > 0) {
                while ($order_items_row = $order_items_result->fetch_assoc()) {

                    $product_id = $order_items_row['PRODUCT_ID'];
                    $product_result = $conn->query("SELECT PRODUCT_NAME, PRODUCT_IMG FROM products WHERE PRODUCT_ID = '$product_id'");
                    $product_details = $product_result->fetch_assoc();
                    $product_name = $product_details['PRODUCT_NAME'];
                    $product_img = 'https://gorder.website/img/products/' . $product_details['PRODUCT_IMG'];

                    $product_check_qty = "SELECT QUANTITY FROM inventory WHERE PRODUCT_ID = '$product_id'";
                    $product_result = $conn->query($product_check_qty);
                    if ($product_result->num_rows > 0) {
                        $qty = 0;
                        while ($product_row = $product_result->fetch_assoc()) {
                            $qty += $product_row['QUANTITY'];
                        }
                        $order_item = [
                            'PRODUCT_ID' => $order_items_row['PRODUCT_ID'],
                            'PRODUCT_NAME' => $product_name,
                            'PRODUCT_IMG' => $product_img,
                            'QTY_LEFT' => intval($qty),
                            'QTY' => intval($order_items_row['QTY']),
                            'AMOUNT' => floatval($order_items_row['AMOUNT'])
                        ];
                    } else {
                        $order_item = [
                            'PRODUCT_ID' => $order_items_row['PRODUCT_ID'],
                            'PRODUCT_NAME' => $product_name,
                            'PRODUCT_IMG' => $product_img,
                            'QTY_LEFT' => 0,
                            'QTY' => intval($order_items_row['QTY']),
                            'AMOUNT' => floatval($order_items_row['AMOUNT'])
                        ];
                    }
                    $order_items_array[] = $order_item;
                }
            } else {
                $data = [
                    'status' => 405,
                    'message' => 'Cart Is Empty',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
                exit;
            }

            $subtotal = 0;
            foreach ($order_items_array as $order_item) {
                $subtotal += $order_item['AMOUNT'];
            }

            $vat = 0;
            $vatable_subtotal = 0;
            foreach ($order_items_array as $order_item) {
                $product_id = $order_item['PRODUCT_ID'];
                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                $product_result = $conn->query($product_sql);
                $product = $product_result->fetch_assoc();

                $isVatable = $product['VATABLE'];

                if ($isVatable == true) {
                    $vatable_subtotal += $order_item['AMOUNT'];
                }
            }

            $tax_percentage_sql = "SELECT * FROM tax WHERE TAX_ID = 1";
            $tax_percentage_result = $conn->query($tax_percentage_sql);
            $tax = $tax_percentage_result->fetch_assoc();
            $taxPercentage = $tax['TAX_PERCENTAGE'];

            $vat = $vatable_subtotal * $taxPercentage;

            $discount = 0;
            if ($discount_type != '') {
                $discountable_subtotal = 0;
                foreach ($order_items_array as $order_item) {
                    $product_id = $order_item['PRODUCT_ID'];
                    $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                    $product_result = $conn->query($product_sql);
                    $product = $product_result->fetch_assoc();

                    $isDiscountable = $product['DISCOUNTABLE'];

                    if ($isDiscountable == true) {
                        $discountable_subtotal += $order_item['AMOUNT'];
                    }
                }

                $discount_percentage_sql = "SELECT * FROM discount WHERE DISCOUNT_ID = '$discount_type'";
                $discount_percentage_result = $conn->query($discount_percentage_sql);
                $discount = $discount_percentage_result->fetch_assoc();
                $discountPercentage = $discount['DISCOUNT_PERCENTAGE'];
                $discount = $discountable_subtotal * $discountPercentage;
            }

            $total = ($subtotal + $vat) - $discount;
            $df_sql = "SELECT DELIVERY_FEE FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
            $df_result = $conn->query($df_sql);
            $delivery = $df_result->fetch_assoc();
            $df = floatval($delivery['DELIVERY_FEE']);

            $data = [
                'status' => 200,
                'message' => 'Computed Price',
                'items' => $order_items_array,
                'order_details' => [
                    'subtotal' => $subtotal,
                    'vat' => $vat,
                    'discount' => $discount,
                    'total' => $total,
                    'delivery_fee' => $df,
                    'total_plus_delivery_fee' => $total + $df,
                ]
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'message' => 'User Acount Deactivated',
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No User Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}


function paymentType($id)
{
    global $conn;

    $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$id'";
    $cust_result = $conn->query($cust_sql);
    if ($cust_result->num_rows > 0) {
        $payment_type_sql = "SELECT * FROM payment_type";
        $payment_type_result = $conn->query($payment_type_sql);
        if ($payment_type_result->num_rows > 0) {
            $payment_types = [];
            while ($payment_type_row = $payment_type_result->fetch_assoc()) {
                $payment = [
                    'type_id' => $payment_type_row['TYPE_ID'],
                    'payment_type' => $payment_type_row['PAYMENT_TYPE']
                ];
                $payment_types[] = $payment;
            }

            $data = [
                'status' => 200,
                'message' => 'Payment Types',
                'payment_types' => $payment_types
            ];
            header("HTTP/1.0 405 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 200,
                'message' => 'No Payment Type Found'
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No User Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}


function placeorder($cust_id, $payment_type, $delivery_type, $unit_st, $bgy_id)
{
    global $conn;
    global $currentTime;
    global $currentDate;

    $cust_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
    $cust_result = $conn->query($cust_sql);
    if ($cust_result->num_rows > 0) {
        $cust = $cust_result->fetch_assoc();
        if ($cust['STATUS'] === "active") {
            $cart_id = $cust['CART_ID'];
            $discount_type = $cust['DISCOUNT_TYPE'];

            $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
            $bgy_result = $conn->query($bgy_sql);
            if ($bgy_result->num_rows > 0) {
                if ($payment_type === 'Cash') {
                    if ($delivery_type === 'Deliver') {
                        $transaction_id = 'ORD-' . mt_rand(10000000, 99999999);
                        $check_trans_id_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
                        $check_trans_id_result = $conn->query($check_trans_id_sql);
                        while ($check_trans_id_result->num_rows > 0) {
                            $transaction_id = 'ORD-' . mt_rand(10000000, 99999999);
                            $check_trans_id_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
                            $check_trans_id_result = $conn->query($check_trans_id_sql);
                        }

                        $order_items_sql = "SELECT * FROM cart_items WHERE CART_ID = '$cart_id'";
                        $order_items_result = $conn->query($order_items_sql);
                        $order_items_array = [];
                        if ($order_items_result->num_rows > 0) {
                            while ($order_items_row = $order_items_result->fetch_assoc()) {

                                $product_id = $order_items_row['PRODUCT_ID'];
                                $order_qty = $order_items_row['QTY'];
                                $inventory_sql = "SELECT QUANTITY FROM inventory WHERE PRODUCT_ID = '$product_id'";
                                $inventory_result = $conn->query($inventory_sql);
                                if ($inventory_result->num_rows > 0) {
                                    $pro_qty = 0;
                                    while ($inventory_row = $inventory_result->fetch_assoc()) {
                                        $pro_qty += $inventory_row['QUANTITY'];
                                    }
                                    if ($pro_qty >= $order_qty) {
                                        $order_item = [
                                            'PRODUCT_ID' => $order_items_row['PRODUCT_ID'],
                                            'QTY_LEFT' => $pro_qty,
                                            'QTY' => $order_items_row['QTY'],
                                            'AMOUNT' => $order_items_row['AMOUNT']
                                        ];
                                    } else {
                                        $data = [
                                            'status' => 200,
                                            'message' => 'Please Enter A Valid Quantity',
                                        ];
                                        header("HTTP/1.0 405 Access Deny");
                                        return json_encode($data);
                                        exit;
                                    }
                                } else {
                                    $data = [
                                        'status' => 200,
                                        'message' => 'Please Enter A Valid Quantity',
                                    ];
                                    header("HTTP/1.0 405 Access Deny");
                                    return json_encode($data);
                                    exit;
                                }
                                $order_items_array[] = $order_item;
                            }
                        } else {
                            $data = [
                                'status' => 405,
                                'message' => 'Cart Is Empty',
                            ];
                            header("HTTP/1.0 405 Access Deny");
                            return json_encode($data);
                            exit;
                        }

                        $subtotal = 0;
                        foreach ($order_items_array as $order_item) {
                            $subtotal += $order_item['AMOUNT'];
                        }

                        $vat = 0;
                        $vatable_subtotal = 0;
                        foreach ($order_items_array as $order_item) {
                            $product_id = $order_item['PRODUCT_ID'];
                            $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                            $product_result = $conn->query($product_sql);
                            $product = $product_result->fetch_assoc();

                            $isVatable = $product['VATABLE'];

                            if ($isVatable == true) {
                                $vatable_subtotal += $order_item['AMOUNT'];
                            }
                        }

                        $tax_percentage_sql = "SELECT * FROM tax WHERE TAX_ID = 1";
                        $tax_percentage_result = $conn->query($tax_percentage_sql);
                        $tax = $tax_percentage_result->fetch_assoc();
                        $taxPercentage = $tax['TAX_PERCENTAGE'];

                        $vat = $vatable_subtotal * $taxPercentage;

                        $discount = 0;
                        if ($discount_type != '') {
                            $discountable_subtotal = 0;
                            foreach ($order_items_array as $order_item) {
                                $product_id = $order_item['PRODUCT_ID'];
                                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                                $product_result = $conn->query($product_sql);
                                $product = $product_result->fetch_assoc();

                                $isDiscountable = $product['DISCOUNTABLE'];

                                if ($isDiscountable == true) {
                                    $discountable_subtotal += $order_item['AMOUNT'];
                                }
                            }

                            $discount_percentage_sql = "SELECT * FROM discount WHERE DISCOUNT_ID = '$discount_type'";
                            $discount_percentage_result = $conn->query($discount_percentage_sql);
                            $discount = $discount_percentage_result->fetch_assoc();
                            $discountPercentage = $discount['DISCOUNT_PERCENTAGE'];
                            $discount = $discountable_subtotal * $discountPercentage;
                        }

                        $total = ($subtotal + $vat) - $discount;

                        $df_sql = "SELECT DELIVERY_FEE FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                        $df_result = $conn->query($df_sql);
                        $delivery = $df_result->fetch_assoc();
                        $df = $delivery['DELIVERY_FEE'];

                        $total += $df;

                        $insert_order_sql = "INSERT INTO `order`(`TRANSACTION_ID`, `CUST_ID`, `PAYMENT_TYPE`, `DELIVERY_TYPE`, `UNIT_STREET`, `BARANGAY_ID`, `TIME`, `DATE`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `STATUS`) 
                                                    VALUES ('$transaction_id','$cust_id','$payment_type','$delivery_type','$unit_st','$bgy_id','$currentTime','$currentDate','$subtotal','$vat','$discount','$total','Waiting')";

                        if ($conn->query($insert_order_sql) === TRUE) {
                            foreach ($order_items_array as $order_item) {
                                $product_id = $order_item['PRODUCT_ID'];
                                $qty = $order_item['QTY'];
                                $amount = $order_item['AMOUNT'];

                                $insert_order_details_sql = "INSERT INTO `order_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QTY`, `AMOUNT`) 
                                                    VALUES ('$transaction_id', '$product_id', '$qty', '$amount')";
                                if ($conn->query($insert_order_details_sql) !== TRUE) {
                                    $data = [
                                        'status' => 200,
                                        'message' => 'Inserting Error'
                                    ];
                                    header("HTTP/1.0 405 OK");
                                    return json_encode($data);
                                }
                            }

                            $delete_cartItems_sql = "DELETE FROM `cart_items` WHERE CART_ID = '$cart_id'";

                            if ($conn->query($delete_cartItems_sql) !== TRUE) {
                            }

                            $data = [
                                'status' => 200,
                                'message' => 'Order Success',
                                'order_items' => $order_items_array,
                                'transaction_id' => $transaction_id,
                                'cust_id' => $cust_id,
                                'payment_type' => $payment_type,
                                'delivery_type' => $delivery_type,
                                'unit_st' => $unit_st,
                                'bgy_id' => $bgy_id,
                                'time' => $currentTime,
                                'date' => $currentDate,
                                'subtotal' => $subtotal,
                                'VAT' => $vat,
                                'discount' => $discount,
                                'total' => $total,
                                'del_status' => 'Waiting',
                                'df' => $df
                            ];
                            header("HTTP/1.0 405 OK");
                            return json_encode($data);
                        } else {
                            $data = [
                                'status' => 200,
                                'message' => 'Inserting Error'
                            ];
                            header("HTTP/1.0 405 OK");
                            return json_encode($data);
                        }
                    } elseif ($delivery_type === 'Pick Up') {
                        $transaction_id = 'ORD-' . mt_rand(10000000, 99999999);
                        $check_trans_id_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
                        $check_trans_id_result = $conn->query($check_trans_id_sql);
                        while ($check_trans_id_result->num_rows > 0) {
                            $transaction_id = 'ORD-' . mt_rand(10000000, 99999999);
                            $check_trans_id_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
                            $check_trans_id_result = $conn->query($check_trans_id_sql);
                        }

                        $order_items_sql = "SELECT * FROM cart_items WHERE CART_ID = '$cart_id'";
                        $order_items_result = $conn->query($order_items_sql);
                        $order_items_array = [];
                        if ($order_items_result->num_rows > 0) {
                            while ($order_items_row = $order_items_result->fetch_assoc()) {

                                $product_id = $order_items_row['PRODUCT_ID'];
                                $order_qty = $order_items_row['QTY'];
                                $inventory_sql = "SELECT QUANTITY FROM inventory WHERE PRODUCT_ID = '$product_id'";
                                $inventory_result = $conn->query($inventory_sql);
                                if ($inventory_result->num_rows > 0) {
                                    $pro_qty = 0;
                                    while ($inventory_row = $inventory_result->fetch_assoc()) {
                                        $pro_qty += $inventory_row['QUANTITY'];
                                    }
                                    if ($pro_qty >= $order_qty) {
                                        $order_item = [
                                            'PRODUCT_ID' => $order_items_row['PRODUCT_ID'],
                                            'QTY_LEFT' => $pro_qty,
                                            'QTY' => $order_items_row['QTY'],
                                            'AMOUNT' => $order_items_row['AMOUNT']
                                        ];
                                    } else {
                                        $data = [
                                            'status' => 200,
                                            'message' => 'Please Enter A Valid Quantity',
                                        ];
                                        header("HTTP/1.0 405 Access Deny");
                                        return json_encode($data);
                                        exit;
                                    }
                                } else {
                                    $data = [
                                        'status' => 200,
                                        'message' => 'Please Enter A Valid Quantity',
                                    ];
                                    header("HTTP/1.0 405 Access Deny");
                                    return json_encode($data);
                                    exit;
                                }
                                $order_items_array[] = $order_item;
                            }
                        } else {
                            $data = [
                                'status' => 405,
                                'message' => 'Cart Is Empty',
                            ];
                            header("HTTP/1.0 405 Access Deny");
                            return json_encode($data);
                            exit;
                        }

                        $subtotal = 0;
                        foreach ($order_items_array as $order_item) {
                            $subtotal += $order_item['AMOUNT'];
                        }

                        $vat = 0;
                        $vatable_subtotal = 0;
                        foreach ($order_items_array as $order_item) {
                            $product_id = $order_item['PRODUCT_ID'];
                            $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                            $product_result = $conn->query($product_sql);
                            $product = $product_result->fetch_assoc();

                            $isVatable = $product['VATABLE'];

                            if ($isVatable == true) {
                                $vatable_subtotal += $order_item['AMOUNT'];
                            }
                        }

                        $tax_percentage_sql = "SELECT * FROM tax WHERE TAX_ID = 1";
                        $tax_percentage_result = $conn->query($tax_percentage_sql);
                        $tax = $tax_percentage_result->fetch_assoc();
                        $taxPercentage = $tax['TAX_PERCENTAGE'];

                        $vat = $vatable_subtotal * $taxPercentage;

                        $discount = 0;
                        if ($discount_type != '') {
                            $discountable_subtotal = 0;
                            foreach ($order_items_array as $order_item) {
                                $product_id = $order_item['PRODUCT_ID'];
                                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                                $product_result = $conn->query($product_sql);
                                $product = $product_result->fetch_assoc();

                                $isDiscountable = $product['DISCOUNTABLE'];

                                if ($isDiscountable == true) {
                                    $discountable_subtotal += $order_item['AMOUNT'];
                                }
                            }

                            $discount_percentage_sql = "SELECT * FROM discount WHERE DISCOUNT_ID = '$discount_type'";
                            $discount_percentage_result = $conn->query($discount_percentage_sql);
                            $discount = $discount_percentage_result->fetch_assoc();
                            $discountPercentage = $discount['DISCOUNT_PERCENTAGE'];
                            $discount = $discountable_subtotal * $discountPercentage;
                        }

                        $total = ($subtotal + $vat) - $discount;

                        $insert_order_sql = "INSERT INTO `order`(`TRANSACTION_ID`, `CUST_ID`, `PAYMENT_TYPE`, `DELIVERY_TYPE`, `UNIT_STREET`, `BARANGAY_ID`, `TIME`, `DATE`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `STATUS`) 
                                                    VALUES ('$transaction_id','$cust_id','$payment_type','$delivery_type','$unit_st','$bgy_id','$currentTime','$currentDate','$subtotal','$vat','$discount','$total','Waiting')";

                        if ($conn->query($insert_order_sql) === TRUE) {
                            foreach ($order_items_array as $order_item) {
                                $product_id = $order_item['PRODUCT_ID'];
                                $qty = $order_item['QTY'];
                                $amount = $order_item['AMOUNT'];

                                $insert_order_details_sql = "INSERT INTO `order_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QTY`, `AMOUNT`) 
                                                    VALUES ('$transaction_id', '$product_id', '$qty', '$amount')";
                                if ($conn->query($insert_order_details_sql) !== TRUE) {
                                    $data = [
                                        'status' => 200,
                                        'message' => 'Inserting Error'
                                    ];
                                    header("HTTP/1.0 405 OK");
                                    return json_encode($data);
                                }
                            }

                            $delete_cartItems_sql = "DELETE FROM `cart_items` WHERE CART_ID = '$cart_id'";

                            if ($conn->query($delete_cartItems_sql) !== TRUE) {
                            }

                            $data = [
                                'status' => 200,
                                'message' => 'Order Success',
                                'order_items' => $order_items_array,
                                'transaction_id' => $transaction_id,
                                'cust_id' => $cust_id,
                                'payment_type' => $payment_type,
                                'delivery_type' => $delivery_type,
                                'unit_st' => $unit_st,
                                'bgy_id' => $bgy_id,
                                'time' => $currentTime,
                                'date' => $currentDate,
                                'subtotal' => $subtotal,
                                'VAT' => $vat,
                                'discount' => $discount,
                                'total' => $total,
                                'del_status' => 'Waiting',
                            ];
                            header("HTTP/1.0 405 OK");
                            return json_encode($data);
                        } else {
                            $data = [
                                'status' => 200,
                                'message' => 'Inserting Error'
                            ];
                            header("HTTP/1.0 405 OK");
                            return json_encode($data);
                        }
                    } else {
                        $data = [
                            'status' => 405,
                            'message' => 'Delivery Type Not Available'
                        ];
                        header("HTTP/1.0 405 OK");
                        return json_encode($data);
                    }
                } elseif ($payment_type === 'Gcash') {
                    $data = [
                        'status' => 405,
                        'message' => 'Gcash Payment is not available'
                    ];
                    header("HTTP/1.0 405 OK");
                    return json_encode($data);
                } else {
                    $data = [
                        'status' => 405,
                        'message' => 'Payment is not available'
                    ];
                    header("HTTP/1.0 405 OK");
                    return json_encode($data);
                }
            } else {
                $data = [
                    'status' => 405,
                    'message' => 'Invalid Barangay ID',
                ];
                header("HTTP/1.0 405 Access Deny");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'User Acount Deactivated',
            ];
            header("HTTP/1.0 405 Access Deny");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No User Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}


//address set
function regions()
{
    global $conn;
    $regions_sql = "SELECT * FROM region WHERE REGION_STATUS = 'active'";
    $regions_result = $conn->query($regions_sql);
    if ($regions_result->num_rows > 0) {
        $regions = [];
        while ($region_row = $regions_result->fetch_assoc()) {
            $regions_data = [
                'region_id' => $region_row['REGION_ID'],
                'region' => $region_row['REGION'],
            ];
            $regions[] = $regions_data;
        }

        $data = [
            'status' => 200,
            'message' => 'All Regions',
            'regions' => $regions
        ];
        header("HTTP/1.0 405 OK");
        return json_encode($data);
    } else {
        $data = [
            'status' => 200,
            'message' => 'No Region Found',
        ];
        header("HTTP/1.0 405 OK");
        return json_encode($data);
    }
}


function orders($user)
{
    global $conn;
    $user_id = $user['id'];

    $customer_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$user_id'";
    $customer_result = $conn->query($customer_sql);
    if ($customer_result->num_rows > 0) {
        $orders_sql = "SELECT * FROM `order` WHERE CUST_ID = '$user_id'";
        $orders_sql_result = $conn->query($orders_sql);
        if ($orders_sql_result->num_rows > 0) {
            $orders = [];
            while ($order_row = $orders_sql_result->fetch_assoc()) {
                $order_data = [
                    'transaction_id' => $order_row['TRANSACTION_ID'],
                    'order_time' => $order_row['TIME'],
                    'order_date' => $order_row['DATE'],
                    'order_status' => $order_row['STATUS'],
                    'price' => $order_row['TOTAL']
                ];
                $orders[] = $order_data;
            }

            $data = [
                'status' => 200,
                'message' => 'All Order Transactions',
                'data' => $orders
            ];
            header("HTTP/1.0 405 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 200,
                'message' => "You haven't placed any orders yet.",
            ];
            header("HTTP/1.0 405 OK");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No User Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}

function order_details($ids)
{
    global $conn;
    $cust_id = $ids['id'];
    $transaction_id = $ids['transaction_id'];

    $customer_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$cust_id'";
    $customer_result = $conn->query($customer_sql);
    if ($customer_result->num_rows > 0) {
        $orders_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
        $orders_sql_result = $conn->query($orders_sql);
        if ($orders_sql_result->num_rows > 0) {
            $order = $orders_sql_result->fetch_assoc();
            $order_details_sql = "SELECT * FROM order_details WHERE TRANSACTION_ID = '$transaction_id'";
            $order_details_result = $conn->query($order_details_sql);
            if ($order_details_result->num_rows > 0) {
                $order_details_array = [];
                while ($order_row = $order_details_result->fetch_assoc()) {
                    $product_id = $order_row['PRODUCT_ID'];
                    $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                    $product_result = $conn->query($product_sql);
                    $product = $product_result->fetch_assoc();

                    $order_details = [
                        'product_name' => $product['PRODUCT_NAME'],
                        'product_img' => 'https://gorder.website/img/products/' . $product['PRODUCT_IMG'],
                        'selling_price' => $product['SELLING_PRICE'],
                        'qty' => $order_row['QTY'],
                        'amount' => $order_row['AMOUNT']
                    ];
                    $order_details_array[] = $order_details;
                }

                $unit_st = $order['UNIT_STREET'];
                $bgy_id = $order['BARANGAY_ID'];

                $barangay = '';
                $df = 0;

                $bgy_sql = "SELECT * FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
                $bgy_result = $conn->query($bgy_sql);
                if ($bgy_result->num_rows > 0) {
                    $bgy = $bgy_result->fetch_assoc();

                    $barangay = $bgy['BARANGAY'];
                    $df = $bgy['DELIVERY_FEE'];
                    $muni_id = $bgy['MUNICIPALITY_ID'];

                    $muni_sql = "SELECT PROVINCE_ID, MUNICIPALITY FROM municipality WHERE MUNICIPALITY_ID = '$muni_id'";
                    $muni_result = $conn->query($muni_sql);
                    $muni = $muni_result->fetch_assoc();

                    $municipality = $muni['MUNICIPALITY'];
                    $prov_id = $muni['PROVINCE_ID'];

                    $prov_sql = "SELECT REGION_ID, PROVINCE FROM province WHERE PROVINCE_ID = '$prov_id'";
                    $prov_result = $conn->query($prov_sql);
                    $prov = $prov_result->fetch_assoc();

                    $province = $prov['PROVINCE'];
                    $reg_id = $prov['REGION_ID'];

                    $reg_sql = "SELECT REGION FROM region WHERE REGION_ID = '$reg_id'";
                    $reg_result = $conn->query($reg_sql);
                    $reg = $reg_result->fetch_assoc();

                    $region = $reg['REGION'];

                    $delivery_address =  $unit_st . ", " . $barangay . ", " . $municipality . ", " . $province . ", " . $region;
                } else {
                    $barangay = 'Barangay That You Selected is Not Available For Delivery!';
                }

                $rider_name = '';
                $rider_id = $order['RIDER_ID'];
                $rider_sql = "SELECT * FROM employee WHERE EMP_TYPE = 'Rider' AND EMP_ID = '$rider_id'";
                $rider_result = $conn->query($rider_sql);
                if ($rider_result->num_rows > 0) {
                    $rider = $rider_result->fetch_assoc();
                    $rider_name = $rider['FIRST_NAME'] . " " . $rider['LAST_NAME'];
                } else {
                    $rider = 'The rider has not been assigned yet.';
                }

                $data = [
                    'status' => 200,
                    'message' => 'Order Details',
                    'orders' => $order_details_array,
                    'transaction_id' => $order['TRANSACTION_ID'],
                    'payment_type' => $order['PAYMENT_TYPE'],
                    'del_type' => $order['DELIVERY_TYPE'],
                    'del_address' => $delivery_address,
                    'order_time' => $order['TIME'],
                    'order_date' => $order['DATE'],
                    'subtotal' => $order['SUBTOTAL'],
                    'vat' => $order['VAT'],
                    'discount' => $order['DISCOUNT'],
                    'delivery_fee' => $df,
                    'total' => $order['TOTAL'],
                    'payment' => $order['PAYMENT'],
                    'change' => $order['CHANGE'],
                    'prescription' => $order['PRESCRIPTION'],
                    'rider' => $rider_name,
                    'order_status' => $order['STATUS']
                ];
                header("HTTP/1.0 405 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 200,
                    'message' => 'No Order Found',
                ];
                header("HTTP/1.0 405 OK");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 405,
                'message' => 'Invalid Transaction ID',
            ];
            header("HTTP/1.0 405 Access Denied");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'No User Found',
        ];
        header("HTTP/1.0 405 Access Deny");
        return json_encode($data);
    }
}
