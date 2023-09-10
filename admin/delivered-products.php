<?php
error_reporting(0);
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();
}

if (isset($_GET['del_id'])) {
    $delID = $_GET['del_id'];

    $del_query = "SELECT * FROM delivery WHERE DELIVERY_ID = $delID";
    $del_query_result = $conn->query($del_query);

    if ($del_query_result->num_rows > 0) {
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
    <link rel="stylesheet" href="../css/delivered-products.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
    <title>GOrder | Deliver</title>
</head>

<body>
    <?php if (isset($emp) && $emp["EMP_TYPE"] == "Admin" && $emp['EMP_STATUS'] == "active" && isset($del)) : ?>

        <div class="alert editing-success bg-success">
            Editing Successful
        </div>
        <div class="alert adding-success bg-success">
            Adding Successful
        </div>
        <div class="alert product-not-exist bg-danger">
            Invalid Product ID
        </div>
        <div class="alert editing-failed bg-danger">
            Editing Failed
        </div>
        <div class="alert adding-failed bg-danger">
            Adding Failed
        </div>
        <div class="alert invalid-exp-date bg-danger">
            Invalid Expiration Date
        </div>
        <div class="alert deletion-unsucc bg-danger">
            Deletion Unsuccessful
        </div>
        <div class="alert deletion-success bg-success">
            Deletion Successful
        </div>
        <div class="alert no-edit bg-danger">
            You cannot edit this delivered item.
        </div>
        <div class="alert no-delete bg-danger">
            You cannot delete this delivered item.
        </div>
        <div class="alert not-exist bg-danger">
            This product does not exist in the database.
        </div>
        <div class="alert problem bg-danger">
            There are a few issues with the connection. Please try again later.
        </div>

        <div class="delivered-container">
            <div class="top-left">
                <a href="products-deliver.php" class="delivery-back"><i class="fa-solid fa-left-long"></i><span>Deliver</span></a>

                <p>Delivery ID: <?php echo $del['DELIVERY_ID'] ?></p>
                <?php
                $suppID = $del['SUPPLIER_ID'];
                $supp_query = "SELECT NAME FROM supplier WHERE SUPPLIER_ID = '$suppID'";
                $supp_result = $conn->query($supp_query);

                if ($supp_result->num_rows > 0) {
                    $supp = $supp_result->fetch_assoc();
                }
                ?>
                <p>Delivery Date: <?php echo $supp['NAME'] ?></p>
                <p>Delivery Date: <?php echo $del['DELIVERY_DATE'] ?></p>
                <p>Delivery Price: <span id="del_price"></span></p>
            </div>

            <div class="top-right">
                <form class="add-product-container">
                    <h5>Add To Inventory</h5>
                    <div class="f-row">

                        <div class="input">
                            <input type="text" class="form-control" name="product_id" id="product_id" placeholder="Search Product Name..." list="pro_ids" required>
                            <label for="product_id">Product ID</label>
                            <datalist id="pro_ids">
                                <?php
                                $products_sql = "SELECT * FROM products WHERE PRODUCT_STATUS = 'active'";
                                $products_result = $conn->query($products_sql);

                                if ($products_result->num_rows > 0) {
                                    while ($row = $products_result->fetch_assoc()) {
                                ?>
                                        <option value="<?php echo $row['PRODUCT_ID'] ?>">
                                            <?php echo $row['PRODUCT_NAME'] . ' - ₱ ' . $row['SELLING_PRICE'] ?>
                                        </option>
                                <?php
                                    }
                                }

                                ?>
                            </datalist>
                        </div>

                        <input type="hidden" id="del_id" name="del_id" value="<?php echo $del['DELIVERY_ID'] ?>">

                        <div class="input">
                            <input type="date" class="form-control" name="expiration_date" id="expiration_date" required>
                            <label for="expriration_date">Expiration Date</label>
                        </div>
                    </div>
                    <div class="s-row">
                        <div class="input">
                            <input type="text" class="form-control" name="supp_price" id="supp_price" placeholder="Enter Supplier Price" maxlength="6" oninput="this.value=this.value.replace(/[^0-9.]/g,'');" required>
                            <label for="supp_price">Price</label>
                        </div>
                        <div class="input">
                            <input type="text" class="form-control" name="del_qty" id="del_qty" placeholder="Delivered Qty" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                            <label for="del_qty">Quantity</label>
                        </div>

                        <div class="">
                            <input type="submit" name="add_delivered" id="add_delivered" value="Add" class="add-btn btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <div class="delivered-products-table-container">
            <table class="delivered-products-table table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Supplier Price</th>
                        <th>Selling Price</th>
                        <th>Mark up</th>
                        <th>Expiration Date</th>
                        <th>Delivered Quantity</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="delivered-containainer">

                </tbody>
            </table>
        </div>

        <form class="edit-product-container">
            <p id="inv-id"></p>
            <input type="hidden" id="inv-id-hidden">
            <button id="closeEditInventory" type="button"><i class="fa-solid fa-xmark"></i></button>
            <center id="product-name"></center>
            <div class="input">
                <input type="date" id="expiration-date" class="form-control">
                <label>Expiration Date</label>
            </div>
            <div class="input">
                <input type="text" class="form-control" id="supp-price" placeholder="Enter Supplier Price" maxlength="6" oninput="this.value=this.value.replace(/[^0-9.]/g,'');" required>
                <label for="supp_price">Price</label>
            </div>
            <div class="input">
                <input type="text" class="form-control" id="edit_del_qty" placeholder="Delivered Qty" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                <label for="del_qty">Quantity</label>
            </div>
            <input type="submit" id="save-change" class="btn btn-primary" value="Save">
        </form>


        <div class="modal" tabindex="-1" role="dialog" id="myModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this delivered item?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="delete-this-delivered" data-inv_id="">Delete</button>
                        <button type="button" class="btn btn-secondary" id="close-delete-this-delivered" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        <p class="emptype-name"><?php echo $emp['EMP_TYPE'] . " : " . $emp['FIRST_NAME'] . " " . $emp["MIDDLE_INITIAL"] . " " . $emp['LAST_NAME'] ?></p>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
        <script src="../js/delivered-products.js"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>