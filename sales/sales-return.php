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

            <body id="return_container">
                <input type="hidden" id="transaction_id_hidden" value="<?php echo $transaction_id ?>">



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
