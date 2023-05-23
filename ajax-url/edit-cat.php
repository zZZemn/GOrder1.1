<?php 

if(isset($_POST['updatedValue']) && isset($_POST['cat_id'])){
    include('../database/db.php');

    $updated_cat_value = filter_var($_POST['updatedValue'], FILTER_SANITIZE_STRING);
    $cat_id = $_POST['cat_id'];

    $cat_sql = "UPDATE `category` SET `CAT_NAME`='$updated_cat_value' WHERE CAT_ID = $cat_id";

    if($conn->query($cat_sql) === TRUE){
        echo 'Edited';
    }else{
        echo 'Edit Not Success';
    }
}