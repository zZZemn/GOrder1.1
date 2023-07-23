<?php 
include('../database/db.php');
if(isset($_POST['cat'])){
    $cat_id = $_POST['cat'];
    $subcat_result = $conn->query("SELECT SUB_CAT_NAME, SUB_CAT_ID FROM sub_category WHERE CAT_ID = '$cat_id'");
    if($subcat_result->num_rows > 0){
        $subcats = [];
        while($subcat = $subcat_result->fetch_assoc()){
            $subcat = [
                'sub_cat_id' => $subcat['SUB_CAT_ID'],
                'sub_cat'=> $subcat['SUB_CAT_NAME']
            ];
            $subcats[] = $subcat;
        }

        echo json_encode($subcats);
    }
    
}
