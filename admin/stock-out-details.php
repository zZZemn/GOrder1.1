<?php
error_reporting(0);
session_start();

function backToLogin()
{
    header('Location: ../index.php');
    exit;
}

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if ($emp["EMP_TYPE"] !== "Admin" || $emp['EMP_STATUS'] !== "active") {
        backToLogin();
    }
} else {
    backToLogin();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stock_out_result = $conn->query("SELECT * FROM `stock_out` WHERE `STOCK_OUT_ID` = '$id'");
    if ($stock_out_result->num_rows > 0) {
        $stock_out = $stock_out_result->fetch_assoc();

        $branch = '';
        $branch_sql = $conn->query("SELECT * FROM `branch` WHERE `ID` = '{$stock_out['BRANCH_ID']}'");
        if ($branch_sql->num_rows > 0) {
            $branch_result = $branch_sql->fetch_assoc();
            $branch = $branch_result['BRANCH'];
        }

        $processBy = '';
        $process_by_sql = $conn->query("SELECT * FROM `employee` WHERE `EMP_ID` = '{$stock_out['EMP_ID']}'");
        if ($process_by_sql->num_rows > 0) {
            $process_by_result = $process_by_sql->fetch_assoc();
            $processBy = $process_by_result['FIRST_NAME'] . ' ' . $process_by_result['MIDDLE_INITITAL'] . ' ' . $process_by_result['LAST_NAME'];
        }
    } else {
        header('Location: products-stock-out.php');
        exit;
    }
} else {
    backToLogin();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <link rel="stylesheet" href="../css/stock-out-details.css">
    <title>Stock Out</title>
</head>

<body>
    <a class="btn-back" href="products-stock-out.php"><i class="fa-solid fa-arrow-left"></i> Back</a>

    <div class="top-container">
        <div class="top-left-conainer">
            <div class="input-container">
                <input class="form-control" type="text" readonly id="stock-out-id" value="<?= $stock_out['STOCK_OUT_ID'] ?>">
                <label for="stock-out-id">Stock Out ID</label>
            </div>
            <div class="input-container">
                <input class="form-control" type="text" readonly id="branch" value="<?= $branch ?>">
                <label for="branch">For Branch</label>
            </div>
            <div class="input-container">
                <input class="form-control" type="text" readonly id="process-by" value="<?= $processBy ?>">
                <label for="process-by">Process By</label>
            </div>
            <div class="input-container">
                <input class="form-control" type="text" readonly id="process-date" value="<?= $stock_out['DATE'] ?>">
                <label for="process-date">Process Date</label>
            </div>
            <div class="input-container">
                <input class="form-control" type="text" readonly id="total" value="<?= $stock_out['TOTAL'] ?>">
                <label for="total">Total Value</label>
            </div>
        </div>
        <form class="top-right-container" id="frm-add-sod">
            <center>Add Product</center>
            <div class="input-container">
                <input class="form-control" type="number" id="product_name">
                <label for="product_name">Total Value</label>
                <div class="invalid-feedback">Please Input Valid Product ID.</div>
            </div>
            <div class="input-container">
                <input class="form-control" type="number" id="qty">
                <label for="qty">Quantity</label>
                <div class="invalid-feedback">Please Input Valid Product ID.</div>
            </div>
            <input class="btn btn-primary" type="submit" value="Add" data-id="<?= $stock_out['STOCK_OUT_ID'] ?>" id="add-sod">
        </form>
    </div>

    <div class="table-container card">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Expiration Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="sod-container">

            </tbody>
        </table>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
    <script src="../js/stock-out-details.js"></script>
</body>

</html>