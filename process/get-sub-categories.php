<?php
include '../database/db.php';

$cat_id = $_POST['cat_id'];

$sub_categories = "SELECT * FROM sub_category WHERE CAT_ID = $cat_id";
$sub_categories_result = $conn->query($sub_categories);

// Build the HTML for the <option> elements
$options = '';
if ($sub_categories_result->num_rows > 0) {
  while ($row = $sub_categories_result->fetch_assoc()) {
    $options .= '<option value="' . $row['SUB_CAT_ID'] . '">' . $row['SUB_CAT_NAME'] . '</option>';
  }
}
else
{
    $options = "<option value=''></option>";
}

// Return the <option> elements as a response
echo $options;

?>
