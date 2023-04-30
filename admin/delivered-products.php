<?php
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

        <div class="delivered-container">
            <div class="top-left">
                <a href="products-deliver.php" class="delivery-back"><i class="fa-solid fa-left-long"></i><span>Delivery</span></a>

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
                <p>Delivery Price: <?php echo $del['DELIVERY_PRICE'] ?></p>
            </div>

            <div class="top-right">
                <form class="add-product-container" action="../process/delivered-add-process.php" method="post">
                    <h5>Add Product</h5>
                    <div class="f-row">

                        <div class="input">
                            <input type="text" name="product_id" id="product_id" placeholder="Search Product Name..." list="pro_ids" required>
                            <label for="product_id">Product ID</label>
                            <datalist id="pro_ids">
                                <?php
                                $products_sql = "SELECT * FROM products WHERE PRODUCT_STATUS = 'active'";
                                $products_result = $conn->query($products_sql);

                                if ($products_result->num_rows > 0) {
                                    while ($row = $products_result->fetch_assoc()) {
                                ?>
                                        <option value="<?php echo $row['PRODUCT_ID'] ?>"><?php echo $row['PRODUCT_NAME'] ?></option>
                                <?php
                                    }
                                }

                                ?>
                            </datalist>
                        </div>

                        <input type="hidden" name="del_id" value="<?php echo $del['DELIVERY_ID'] ?>">

                        <div class="input">
                            <input type="date" name="expriration_date" id="expriration_date">
                            <label for="expriration_date">Expiration Date</label>
                        </div>

                        <div class="input">
                            <input type="number" name="batch_no" id="batch_no" placeholder="Enter Batch No.">
                            <label for="batch_no">Batch No</label>
                        </div>
                    </div>
                    <div class="s-row">
                        <div class="input">
                            <input type="number" name="supp_price" id="supp_price" placeholder="Enter Supplier Price">
                            <label for="supp_price">Price</label>
                        </div>
                        <div class="input">
                            <input type="number" name="del_qty" id="del_qty" placeholder="Delivered Qty">
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
                        <th>Expiration Date</th>
                        <th>Selling Price</th>
                        <th>Qty</th>
                        <th>Del Qty</th>
                        <th>Batch No.</th>
                        <th>Mark up</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $delID = $_GET['del_id'];
                    $del_query = "SELECT * FROM inventory WHERE DELIVERY_ID = $delID";
                    $del_query_result = $conn->query($del_query);
                    if($del_query_result->num_rows > 0) {
                        while($row = $del_query_result->fetch_assoc()){
                            $pro_id = $row['PRODUCT_ID'];
                            $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = $pro_id";
                            $product_result = $conn->query($product_sql);
                            $product = $product_result->fetch_assoc();
                            ?>
                    <tr>
                        <td><?php echo $product['PRODUCT_NAME'] ?></td>
                        <td><?php echo $row['SUPPLIER_PRICE'] ?></td>
                        <td><?php echo $row['EXP_DATE'] ?></td>
                        <td><?php echo $product['SELLING_PRICE'] ?></td>
                        <td><?php echo $row['QUANTITY'] ?></td>
                        <td><?php echo $row['DEL_QUANTITY'] ?></td>
                        <td><?php echo $row['BATCH_NO'] ?></td>
                        <td><?php echo $row['MARK_UP'] ?></td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>

                            <?php
                        }
                    }
                    ?>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>abcdefg</td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>



        <p class="emptype-name"><?php echo $emp['EMP_TYPE'] . " : " . $emp['FIRST_NAME'] . " " . $emp["MIDDLE_INITIAL"] . " " . $emp['LAST_NAME'] ?></p>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>

    <?php else : ?>
        <div class="access-denied">
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
    <?php endif; ?>
</body>

</html>