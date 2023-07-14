<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_GET['search'])) {
            $search = $_GET['search'];

            if ($search !== '') {
                $sql = "SELECT * FROM supplier WHERE `NAME` LIKE '%$search%' AND SUPPLIER_STATUS = 'active'";
            } else {
                $sql = "SELECT * FROM supplier WHERE SUPPLIER_STATUS = 'active'";
            }

            if ($sup_result = $conn->query($sql)) {
                if ($sup_result->num_rows > 0) {
                    while ($row = $sup_result->fetch_assoc()) {
?>
                        <div class="supplier-container">
                            <div class="supplier-f-row">
                                <h5><?php echo $row['NAME'] ?></h5>
                                <h5><?php echo $row['SUPPLIER_ID'] ?></h5>
                            </div>

                            <div class="supplier-s-row">
                                <div class="supplier-s-row-bottom">
                                    <div>
                                        <p class="head">Address</p>
                                        <p><?php echo $row['ADDRESS'] ?></p>
                                    </div>
                                    <div>
                                        <p class="head">Contact Person</p>
                                        <p><?php echo $row['CONTACT_PERSON'] ?></p>
                                    </div>
                                    <div>
                                        <p class="head">Contact Number</p>
                                        <p><?php echo $row['CONTACT_NO'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="add-edit-container">
                                <a href="#" id="btn-edit-supplier" data-supplier_id="<?php echo $row['SUPPLIER_ID'] ?>">Edit <i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="#" id="btn-delete-supplier" data-supplier_id="<?php echo $row['SUPPLIER_ID'] ?>">Delete <i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <center class="no-sup-found">No Supplier Found</center>
                <?php
                }
            } else {
                ?>
                <center class="no-sup-found">No Supplier Found</center>
<?php
            }
        } else {
            echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
