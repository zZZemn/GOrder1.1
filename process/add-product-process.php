<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['addProduct'])) {
        include('../time-date.php');

        $product_id = mt_rand(10000000, 99999999);
        $checkingProduct_id = "SELECT * FROM products WHERE PRODUCT_ID = $product_id";
        $checkingProduct_id_result = $conn->query($checkingProduct_id);
        while ($checkingProduct_id_result->num_rows > 0) {
            $product_id = mt_rand(10000000, 99999999);
            $checkingProduct_id = "SELECT * FROM products WHERE PRODUCT_ID = $product_id";
            $checkingProduct_id_result = $conn->query($checkingProduct_id);
        }

        // Set the file name based on whether the user uploaded a picture or not
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
            $file_name = trim($product_id) . '.' . pathinfo(trim($_FILES['product_image']['name']), PATHINFO_EXTENSION);
            $tmp_name = $_FILES['product_image']['tmp_name'];
            $file_destination = '../img/products/' . $file_name;

            // Check for errors during file upload
            if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
                header("Location: ../admin/products-allproducts.php?status=invalid_upload");
                exit();
            }

            // Check file size
            if ($_FILES['product_image']['size'] > 5000000) {
                header("Location: ../admin/products-allproducts.php?status=invalid_size");
                exit();
            }

            if (!move_uploaded_file($tmp_name, $file_destination)) {
                header("Location: ../admin/products-allproducts.php?status=invalid_upload");
                exit();
            }
        } else {
            $file_name = 'product_default_img.png';
        }

        $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
        $product_code = filter_var($_POST['product_code'], FILTER_SANITIZE_STRING);
        $g = filter_var($_POST['g'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $mg = filter_var($_POST['mg'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $ml = filter_var($_POST['ml'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $critical_level = filter_var($_POST['critical_level_add'], FILTER_SANITIZE_NUMBER_INT);
        $selling_price = filter_var($_POST['selling_price_add'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $sub_category_id = filter_var($_POST['sub_cat'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $is_prescribed = isset($_POST['prescribe']) ? 1 : 0;
        $is_vatable = isset($_POST['vatable']) ? 1 : 0;
        $is_discountable = isset($_POST['discountable']) ? 1 : 0;
        $is_sellOnline = isset($_POST['SellInGorder']) ? 1 : 0;

        if (empty($sub_category_id)) {
            $insert_products = "INSERT INTO `products`(`PRODUCT_ID`, `PRODUCT_CODE`, `PRODUCT_NAME`, `G`, `MG`, `ML`, `SELLING_PRICE`, `DESCRIPTION`, `CRITICAL_LEVEL`, `PRODUCT_IMG`, `PRESCRIBE`, `VATABLE`,`DISCOUNTABLE` ,`SELL_IN_GORDER` , `PRODUCT_STATUS`) 
            VALUES ('$product_id','$product_code','$product_name','$g','$mg','$ml','$selling_price','$description','$critical_level','$file_name','$is_prescribed','$is_vatable','$is_discountable','$is_sellOnline','active')";
        } else {
            $insert_products = "INSERT INTO `products`(`PRODUCT_ID`, `PRODUCT_CODE`, `PRODUCT_NAME`, `G`, `MG`, `ML`, `SELLING_PRICE`, `SUB_CAT_ID`, `DESCRIPTION`, `CRITICAL_LEVEL`, `PRODUCT_IMG`, `PRESCRIBE`, `VATABLE`,`DISCOUNTABLE` ,`SELL_IN_GORDER` , `PRODUCT_STATUS`) 
            VALUES ('$product_id','$product_code','$product_name','$g','$mg','$ml','$selling_price', '$sub_category_id','$description','$critical_level','$file_name','$is_prescribed','$is_vatable','$is_discountable','$is_sellOnline','active')";
        }

        $addDate = $currentDate;
        $addTime = $currentTime;
        $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

        $add_pro_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Add $product_name in product list.','$addDate','$addTime')";

        if ($conn->query($insert_products) === TRUE && $conn->query($add_pro_log) === TRUE) {
            header("Location: ../admin/products-allproducts.php?status=success");
            exit();
        } else {
            header("Location: ../admin/products-allproducts.php?status=invalid_add");
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
