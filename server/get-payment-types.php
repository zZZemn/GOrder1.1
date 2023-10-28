<?php
if (isset($_GET['id'])) {
    include('../database/db.php');
    $sql = "SELECT * FROM `payment_type`";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
?>
            <tr>
                <td class="<?= ($row['STATUS'] != 'active') ? 'text-danger' : '' ?>"><?= $row['PAYMENT_TYPE'] ?></td>
                <td><?= $row['BANK_NUMBER'] ?></td>
                <td class="btn-td">
                    <?php
                    if ($row['PAYMENT_TYPE'] != 'Cash') {
                        echo '<button type="button" data_id="' . $row['TYPE_ID'] . '" data_name="' . $row['PAYMENT_TYPE'] . '" data_number="' . $row['BANK_NUMBER'] . '" class="btn btn-dark m-1 edit-bank-no">Edit</button>';
                        echo ($row['STATUS'] == 'active')
                            ? '<button type="button" data_id="' . $row['TYPE_ID'] . '" id="deactivate" class="btn btn-danger btnChangeStatus" data_action="deactivated">Disable</button>'
                            : '<button type="button" data_id="' . $row['TYPE_ID'] . '" id="activate" class="btn btn-success btnChangeStatus" data_action="active">Enable</button>';
                    }
                    ?>
                </td>
            </tr>
<?php
        }
    }
} else {
    echo 'asd';
}
