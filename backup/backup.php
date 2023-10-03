<?php
error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
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
    <link rel="stylesheet" href="../css/access-denied.css">
    <link rel="stylesheet" href="../css/loading.css">
    <link rel="stylesheet" href="../css/backup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Backup and Restore</title>
</head>

<body>
    <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active") : ?>
        <div class="alert alert-success bg-success text-light">asd</div>
        <div class="alert alert-danger bg-danger text-light">asd</div>
        <center class="mt-5">
            <h5>Backup And Restore</h5>
        </center>
        <div class="container mt-5">
            <select class="form-control" id="selectedTable">
                <option value="products">Products</option>
                <option value="delivery">Delivery</option>
                <option value="supplier">Supplier</option>
            </select>
        </div>

        <div class="selected-container container" id="selectedContainer">

        </div>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
    <script src="../js/backup.js"></script>
</body>

</html>