<?php 

if(isset($_POST['updatedValue']) && isset($_POST['sub_cat_id'])){
    include('../database/db.php');

    $updated_cat_value = filter_var($_POST['updatedValue'], FILTER_SANITIZE_STRING);
    $sub_cat_id = $_POST['sub_cat_id'];

    $sub_cat_sql = "UPDATE `sub_category` SET `SUB_CAT_NAME`='$updated_cat_value' WHERE SUB_CAT_ID = $sub_cat_id";

    if($conn->query($sub_cat_sql) === TRUE){
        echo 'edited';
    }else{
        echo 'invalid';
    }
}