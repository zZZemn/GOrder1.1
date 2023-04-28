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
        include("../database/db.php");

        $del_id = $_GET['del_id'];

        $sql = "SELECT * FROM delivery WHERE DELIVERY_ID = $del_id";
        $result = $conn->query($sql);
        if($result->num_rows > 0)
        {
            $delivery = $result->fetch_assoc();
        }
    }
?>

<?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active" && isset($delivery['DELIVERY_ID']) && isset($delivery['DELIVERY_DATE']) && isset($delivery['SUPPLIER_ID'])): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete <?php echo $delivery['DELIVERY_ID'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="../css/delete-product.css" rel="stylesheet">
    <link href="../css/access-denied.css" rel="stylesheet">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
</head>

    <body class="container p-5 d-flex justify-content-center align-items-center bg-primary">

        <div class="del-container mt-5 row d-flex text-center" method="post">
            <p>Are you sure you want to delete delivery id <em><?php echo $delivery['DELIVERY_ID']; ?></em> in delivery list?</p>

            <div class="button mt-5">
                <a href="products-deliver.php" class="btn btn-primary m-2">Cancel</a>
                <a href="../process/delete-deliver-process.php?del_id=<?php echo $delivery['DELIVERY_ID'] ?>" class="btn btn-danger m-2">Delete</a>
            </div>
        </div>
    
                            
    <?php else: ?>
        <head>
            <title>Access Denied</title>
            <link href="../css/access-denied.css" rel="stylesheet">
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
            </style>
        </head>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>    
    </body>
</html>