<?php
include '../database/db.php';

$cat_id = $_POST['cat_id'];
$product_id = $_POST['product_id'];

$sub_categories = "SELECT * FROM sub_category WHERE CAT_ID = $cat_id";
$sub_categories_result = $conn->query($sub_categories);

$product_sql = "SELECT SUB_CAT_ID FROM products WHERE PRODUCT_ID = $product_id";
$product_sql_result = $conn->query($product_sql);
$product = $product_sql_result->fetch_assoc();

// Build the HTML for the <option> elements
$options = '';
if ($sub_categories_result->num_rows > 0) {
  while ($row = $sub_categories_result->fetch_assoc()) {
    $options .= '<option value="' . $row['SUB_CAT_ID'] . '"';
    if ($product['SUB_CAT_ID'] == $row['SUB_CAT_ID']) {
    $options .= ' selected';
    }
    $options .= '>' . $row['SUB_CAT_NAME'] . '</option>';
  }
}
else
{
    $options = "<option value=''></option>";
}

// Return the <option> elements as a response
echo $options;

?>
