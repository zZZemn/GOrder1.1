<?php 

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if(isset($_POST['add_delivered'])) {

    $inv_id = mt_rand(100000, 999999);
    $check_inv_id = "SELECT * FROM inventory WHERE INV_ID = $inv_id";
    $check_inv_id_result = $conn->query($check_inv_id);
    while ($check_inv_id_result->num_rows > 0) {
        $inv_id = mt_rand(100000, 999999);
        $check_inv_id = "SELECT * FROM inventory WHERE INV_ID = $inv_id";
        $check_inv_id_result = $conn->query($check_inv_id);
    }

    $del_id = $_POST['del_id'];
    $product_id = $_POST['product_id'];
    $expriration_date = $_POST['expriration_date'];
    $batch_no = $_POST['batch_no'];
    $supp_price = $_POST['supp_price'];
    $del_qty = $_POST['del_qty'];

    $qty = $del_qty;

    $check_pro_price = "SELECT SELLING_PRICE FROM products WHERE PRODUCT_ID = $product_id";
    $check_pro_price_result = $conn->query($check_pro_price);
    if($check_pro_price_result->num_rows > 0)
    {
        $product = $check_pro_price_result->fetch_assoc();
        $selling_price = $product['SELLING_PRICE'];

        $mark_up = $selling_price - $supp_price; 
    }
    else {
        $mark_up = 0;
    }

    $insert_new_delivered = "INSERT INTO `inventory`(`INV_ID`, `DELIVERY_ID`, `PRODUCT_ID`, `SUPPLIER_PRICE`, `QUANTITY`, `EXP_DATE`, `DEL_QUANTITY`, `BATCH_NO`, `MARK_UP`) 
                                             VALUES ('$inv_id','$del_id','$product_id','$supp_price','$qty','$expriration_date','$del_qty','$batch_no','$mark_up')";

    if($conn->query($insert_new_delivered) === TRUE){
        header("Location: ../admin/delivered-products.php?del_id=$del_id&status=success");
        exit;
    } else {
        header("Location: ../admin/delivered-products.php?del_id=$del_id&status=failed");
        exit;
    }
}
else {
    header("Location: ../admin/delivered-products.php?status=failed");
    exit;
}