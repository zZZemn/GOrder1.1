<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

?>
    <div class="top-contents-container">
        <img src="../img/userprofile/<?php echo $emp['PICTURE'] ?>">

        <div class="top-right-contents">
            <?php if ($emp['SEX'] === "m") {
                $sex = "Male";
            } else {
                $sex = "Female";
            } ?>
            <h5 class="full-name"><?php echo $emp['FIRST_NAME'] . " " . $emp['MIDDLE_INITIAL'] . " " . $emp['LAST_NAME'] . " " . $emp['SUFFIX'] . "<em class='sex'>" . $sex . "</em>" ?></h5>


            <p class="emptype"><?= ($emp['EMP_ID'] == '11111') ? 'Super Admin' : $emp['EMP_TYPE']; ?></p>

        </div>
    </div>

    <hr class="profile-line">

    <div class="middle-contents-container">
        <div class="middle-left-contents">
            <div class="contents-input-container">
                <input class="profile-content" value="<?php echo $emp['USERNAME'] ?>" readonly>
                <label class="profile-contents-label">Username</label>
            </div>
            <div class="contents-input-container">
                <input class="profile-content" value="<?php echo $emp['EMAIL'] ?>" readonly>
                <label class="profile-contents-label">Email</label>
            </div>
        </div>

        <div class="middle-right-content">
            <div class="contents-input-container">
                <input class="profile-content" value="<?php echo $emp['CONTACT_NO'] ?>" readonly>
                <label class="profile-contents-label">Contact No.</label>
            </div>
            <div class="contents-input-container">
                <input class="profile-content" value="<?php echo $emp['BIRTHDAY'] ?>" readonly>
                <label class="profile-contents-label">Birthday</label>
            </div>
        </div>
    </div>

    <hr class="profile-line">

    <div class="bottom-contents-container">
        <div class="contents-input-container">
            <input class="profile-content" value="<?php echo $emp['ADDRESS'] ?>" readonly>
            <label class="profile-contents-label">Address</label>
        </div>
    </div>

    <form class="frm-edit-profile card p-3" method="POST" action="process.php"> <!-- Make sure to set the 'action' attribute to the PHP processing script -->
        <center>Edit Profile</center>
        <div class="row f-row">
            <div class="input-container">
                <input type="text" id="fname" name="fname" required class="form-control" value="<?= $emp['FIRST_NAME'] ?>">
                <label for="fname">First Name</label>
            </div>
            <div class="input-container">
                <input type="text" id="lname" name="lname" required class="form-control" value="<?= $emp['LAST_NAME'] ?>">
                <label for="lname">Last Name</label>
            </div>
            <div class="input-container">
                <input type="text" id="mi" name="mi" class="form-control" value="<?= $emp['MIDDLE_INITIAL'] ?>">
                <label for="mi">MI</label>
            </div>
            <div class="input-container">
                <input type="text" id="suffix" name="suffix" class="form-control" value="<?= $emp['SUFFIX'] ?>">
                <label for="suffix">Suffix</label>
            </div>
        </div>
        <div class="row s-row">
            <div class="input-container">
                <select id="sex" name="sex" required class="form-control">
                    <option value="m" <?= ($emp['SEX'] === 'm') ? 'selected' : '' ?>>Male</option>
                    <option value="f" <?= ($emp['SEX'] === 'f') ? 'selected' : '' ?>>Female</option>
                </select>
                <label for="sex">Sex</label>
            </div>
            <div class="input-container">
                <input type="date" id="birthday" name="birthday" required class="form-control" value="<?= $emp['BIRTHDAY'] ?>">
                <label for="birthday">Birthday</label>
            </div>
        </div>
        <div class="row t-row">
            <div class="input-container">
                <input type="text" id="username" name="username" required class="form-control" value="<?= $emp['USERNAME'] ?>">
                <label for="username">Username</label>
            </div>
            <div class="input-container">
                <input type="number" id="contact" name="contact" required class="form-control" value="<?= $emp['CONTACT_NO'] ?>">
                <label for="contact">Contact #</label>
            </div>
            <div class="input-container">
                <input type="email" id="email" name="email" required class="form-control" value="<?= $emp['EMAIL'] ?>">
                <label for="email">Email</label>
            </div>
        </div>
        <div class="row fth-row">
            <div class="input-container address-textarea">
                <textarea id="address" name="address" class="form-control"><?= $emp['ADDRESS'] ?></textarea>
                <label for="address">Address</label>
            </div>
        </div>
        <div class="row fifth-row">
            <button type="button" id="close-frm-edit-profile" class="btn btn-dark">Cancel</button>
            <input type="submit" value="Save" id="save-user-profile" class="btn btn-primary">
        </div>
    </form>
<?php
} else {
    header("Location: ../index.php");
    exit;
}
