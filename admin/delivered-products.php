<?php 
session_start();

if(isset($_SESSION['id']))
{
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if(isset($_GET['del_id']))
{
    $delID = $_GET['del_id'];

    $del_query = "SELECT * FROM delivery WHERE DELIVERY_ID = $delID";
    $del_query_result = $conn->query($del_query);
    
    if($del_query_result->num_rows > 0)
    {
        $del = $del_query_result->fetch_assoc();
    }

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
        </style>
        <link rel="stylesheet" href="../css/nav.css">
        <link rel="stylesheet" href="../css/access-denied.css">
        <link rel="stylesheet" href="../css/message.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
        <title>GOrder | Deliver</title>
    </head>
    <body>
        <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active" && isset($del)): ?>
            
            <div class="delivered-container">
                <div class="top-left">
                    <a href="products-deliver.php" class="delivery-back"><i class="fa-solid fa-left-long"></i><span>Delivery</span></a>

                    <div class="delivery-details">
                        <p>Delivery ID: <?php echo $del['DELIVERY_ID'] ?></p>
                        <?php 
                            $suppID = $del['SUPPLIER_ID'];
                            $supp_query = "SELECT NAME FROM supplier WHERE SUPPLIER_ID = $suppID";
                            
                        ?>
                    </div>
                </div>


            </div>

    

    <p class="emptype-name"><?php echo $emp['EMP_TYPE'] ." : ". $emp['FIRST_NAME'] ." ". $emp["MIDDLE_INITIAL"] ." ". $emp['LAST_NAME']?></p>

        <script  src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
        
    <?php else: ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
    </body>
</html>