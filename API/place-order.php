<?php
header('Acces-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include('functions.php');
include('../database/db.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == "POST") {

    $cust_id = $_POST['cust_id'];
    $payment_type = $_POST['payment_type'];
    $delivery_type = $_POST['delivery_type'];
    $unit_st = $_POST['unit_st'];
    $bgy_id = $_POST['bgy_id'];

    //check bgy
    $bgy_check_sql = "SELECT BARANGAY_STATUS FROM barangay WHERE BARANGAY_ID = '$bgy_id'";
    $bgy_check_result = $conn->query($bgy_check_sql);
    if ($bgy_check_result->num_rows > 0) {
        $bgy = $bgy_check_result->fetch_assoc();
        $bgy_status = $bgy['BARANGAY_STATUS'];
        if ($bgy_status !== 'active') {
            $data = [
                'status' => 405,
                'message' => 'Address Out of Coverage',
            ];
            header("HTTP/1.0 405 invalid");
            echo json_encode($data);
            exit;
        } else {
            $cust_sql = "SELECT CART_ID FROM customer_user WHERE CUST_ID = '$cust_id'";
            $cust_result = $conn->query($cust_sql);
            if ($cust_result->num_rows > 0) {
                $cust = $cust_result->fetch_assoc();
                $cart_id = $cust['CART_ID'];

                $cart_sql = "SELECT PRODUCT_ID, QTY FROM cart_items WHERE CART_ID = '$cart_id'";
                $cart_result = $conn->query($cart_sql);
                if ($cart_result->num_rows > 0) {
                    $invalidQTY = [];
                    $outOfStock = [];
                    $prescribe_products = 0;
                    while ($cart = $cart_result->fetch_assoc()) {
                        $product_id = $cart['PRODUCT_ID'];
                        $qty = $cart['QTY'];

                        $product_result = $conn->query("SELECT PRODUCT_NAME, PRESCRIBE FROM products WHERE PRODUCT_ID = '$product_id'");
                        if ($product_result->num_rows > 0) {
                            $product = $product_result->fetch_assoc();
                            $product_name = $product['PRODUCT_NAME'];
                            $isPrescibe = $product['PRESCRIBE'];
                            if ($isPrescibe == 1) {
                                $prescribe_products++;
                            }
                        } else {
                            $data = [
                                'status' => 405,
                                'message' => 'Invalid Product ID',
                            ];
                            header("HTTP/1.0 405 invalid");
                            echo json_encode($data);
                        }

                        $inv_sql = "SELECT SUM(QUANTITY) AS total_quantity FROM inventory WHERE PRODUCT_ID = '$product_id'";
                        $inv_result = $conn->query($inv_sql);
                        $invalidQTYALL = [];
                        if ($inv_result->num_rows > 0) {
                            $inv = $inv_result->fetch_assoc();
                            $totalQTY = $inv['total_quantity'];

                            $order_sql = "SELECT od.*
                          FROM `order_details` od
                          JOIN `order` o ON od.TRANSACTION_ID = o.TRANSACTION_ID
                          WHERE od.PRODUCT_ID = '$product_id' AND (o.STATUS = 'Waiting' OR o.STATUS = 'Accepted');
                          ";
                            $order_result = $conn->query($order_sql);
                            if ($order_result->num_rows > 0) {
                                while ($order_row = $order_result->fetch_assoc()) {
                                    $totalQTY -= $order_row['QTY'];
                                }
                            }

                            if ($totalQTY <= 0) {
                                $outOfStock[] = $product_name;
                            } elseif ($totalQTY < $qty) {
                                $invalidQTYALL[] = [
                                    'product_name' => $product_name,
                                    'qty_left' => $totalQTY,
                                    'qty_you_want' => $qty
                                ];
                            }
                        } else {
                            $outOfStock[] = $product_name;
                        }

                        if (!empty($invalidQTYALL)) {
                            $invalidQTY = $invalidQTYALL;
                        }
                    }

                    if (!empty($invalidQTY) ||   !empty($outOfStock)) {
                        $data = [
                            'status' => 405,
                            'message' => 'Invalid Placing order',
                            'outofstock_items' => $outOfStock,
                            'invalid_quantity' => $invalidQTY
                        ];
                        header("HTTP/1.0 405 Cart Response");
                        echo json_encode($data);
                    } else {
                        if ($prescribe_products > 0) {
                            if (isset($_FILES['prescription'])) {
                                $prescription = $_FILES['prescription'];
                                $placeorder = placeorderWithPrescription($cust_id, $payment_type, $delivery_type, $unit_st, $bgy_id, $prescription);
                                echo $placeorder;
                            } else {
                                $data = [
                                    'status' => 405,
                                    'message' => 'Please Upload Prescription',
                                ];
                                header("HTTP/1.0 405 No prescription");
                                echo json_encode($data);
                            }
                        } else {
                            if ($payment_type === 'Cash') {
                                $placeorder = placeorder($cust_id, $payment_type, $delivery_type, $unit_st, $bgy_id);
                                echo $placeorder;
                            } else {
                                if (isset($_FILES['pof'])) {
                                    $pof = $_FILES['pof'];
                                    $placeorder = placeorderWithPOF($cust_id, $payment_type, $delivery_type, $unit_st, $bgy_id, $pof);
                                    echo $placeorder;
                                } else {
                                    $data = [
                                        'status' => 405,
                                        'message' => 'Please Upload Proof of Payment',
                                    ];
                                    header("HTTP/1.0 405 No prescription");
                                    echo json_encode($data);
                                }
                            }
                        }
                    }
                } else {
                    $data = [
                        'status' => 405,
                        'message' => 'Invalid Place order, Cart is empty.',
                    ];
                    header("HTTP/1.0 405 Empty Cart");
                    echo json_encode($data);
                }
            } else {
                $data = [
                    'status' => 405,
                    'message' => 'No cust found',
                ];
                header("HTTP/1.0 405 No cust found");
                echo json_encode($data);
            }
        }
    } else {
        $data = [
            'status' => 405,
            'message' => 'Barangay Not Found in The databse',
        ];
        header("HTTP/1.0 405 invalid");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
