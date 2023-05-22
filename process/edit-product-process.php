<?php 

session_start();
if(isset($_SESSION['id']))
{
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if(isset($_POST['save_change']))
{
    include('../time-date.php');

    $productID = $_POST['product_id'];

    $products = "SELECT * FROM products WHERE PRODUCT_ID = $productID";
    $products_result = $conn->query($products);
    $product = $products_result->fetch_assoc();

    $product_img_name = $product['PRODUCT_IMG'];
    $file_name = '';

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        $file_name = trim($productID) . '.' . pathinfo(trim($_FILES['product_image']['name']), PATHINFO_EXTENSION);
        $tmp_name = $_FILES['product_image']['tmp_name'];
        $file_destination = '../img/products/' . $file_name;

        // Check for errors during file upload
        if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../admin/products-allproducts-edit.php?product_id=$productID&status=invalid_image_upload");
            exit();
        }

        // Check file size
        if ($_FILES['product_image']['size'] > 5000000) {
            header("Location: ../admin/products-allproducts-edit.php?product_id=$productID&status=invalid_image_size");
            exit();
        }

        if($product_img_name != "product_default_img.png")
        {
            $file = '../img/products/'.$product_img_name;
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if (!move_uploaded_file($tmp_name, $file_destination)) {
            header("Location: ../admin/products-allproducts-edit.php?product_id=$productID&status=invalid_image_upload");
            exit();
        }
    }

    
    if($file_name == '') {
        $file_name = $product_img_name;
    }

    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_code = filter_input(INPUT_POST, 'product_code', FILTER_SANITIZE_STRING);
    $unit_meas = filter_input(INPUT_POST, 'product_meas', FILTER_SANITIZE_STRING);
    $critical_level = filter_input(INPUT_POST, 'critical_level', FILTER_SANITIZE_NUMBER_INT);
    $selling_price = filter_input(INPUT_POST, 'selling_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category_id = filter_input(INPUT_POST, 'cat', FILTER_SANITIZE_NUMBER_INT);
    $sub_category_id = filter_input(INPUT_POST, 'sub_cat', FILTER_SANITIZE_NUMBER_INT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $is_prescribed = isset($_POST['prescribe']) ? 1 : 0;
    $is_vatable = isset($_POST['vatable']) ? 1 : 0;
    $is_discountable = isset($_POST['discountable']) ? 1 : 0;


    $product_code_final = !empty($product_code) ? $product_code : null;

    $update_product = "UPDATE `products` SET `PRODUCT_CODE`= '$product_code_final',`PRODUCT_NAME`='$product_name',`UNIT_MEASUREMENT`='$unit_meas',`SELLING_PRICE`='$selling_price', `SUB_CAT_ID`='$sub_category_id',`DESCRIPTION`='$description',`CRITICAL_LEVEL`='$critical_level',`PRODUCT_IMG`='$file_name',`PRESCRIBE`='$is_prescribed',`VATABLE`='$is_vatable', `DISCOUNTABLE`='$is_discountable' WHERE PRODUCT_ID = $productID";

    $addDate = $currentDate;
    $addTime = $currentTime;
    $emp_id = isset($emp['EMP_ID']) ? $emp['EMP_ID'] : null;

    $edit_pro_log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                    ('$emp_id','Add $product_name in product list.','$addDate','$addTime')";


    if ($conn->query($update_product) === TRUE && $conn->query($edit_pro_log) === TRUE) {
        header("Location: ../admin/products-allproducts-edit.php?product_id=$productID&status=edited&unitmeas=$unit_meas");
        exit();
    } else {
        header("Location: ../admin/products-allproducts-edit.php?product_id=$productID&status=invalid_edit");
        exit();
    }
}