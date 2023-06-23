<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['id'])) {
            $transaction_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
?>

            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
                </style>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
                <link rel="stylesheet" href="../css/sales-return.css">
                <link rel="shortcut icon" href="../img/ggd-logo-plain.png" type="image/x-icon">
                <title>GOrder | Return</title>
            </head>

            <body>
                <input type="hidden" id="transaction_id_hidden" value="<?php echo $transaction_id ?>">
                <?php 
                $sales_sql = "SELECT CUST_TYPE FROM SALES WHERE TRANSACTION_ID = '$transaction_id'";
                $sales_result = $conn->query($sales_sql);
                if($sales_result->num_rows > 0){
                    $sales = $sales_result->fetch_assoc();
                    $cust_type = $sales['CUST_TYPE'];

                    $discount_sql = "SELECT DISCOUNT_PERCENTAGE FROM discount WHERE DISCOUNT_NAME = '$cust_type'";
                    $discount_result = $conn->query($discount_sql);
                    if($discount_result->num_rows > 0){
                        $discount = $discount_result->fetch_assoc();
                        $discount_percentage = $discount['DISCOUNT_PERCENTAGE'];
                    } else {
                        $discount_percentage = 0.00;
                    }
                } else {
                    $discount_percentage = 0.00;
                }
                ?>
                <input type="hidden" id="discount_percentage" value="<?php echo $discount_percentage ?>">
                <div id="return_container">

                </div>

                <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirm Return</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to add this return?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelAddReturn">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmAddReturn">Add Return</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://kit.fontawesome.com/c6c8edc460.js" crossorigin="anonymous"></script>
                <script src="../js/sales-return.js"></script>
            </body>
<?php
        }
    } else {
        echo '
            <head>
            <link rel="stylesheet" href="../css/access-denied.css">
            </head>
            <div class="access-denied">
                  <h1>Access Denied</h1>
                  <h5>Invalid to access this page.</h5>
              </div>';
    }
} else {
    header("Location: ../index.php");
    exit;
}
