<?php
include('database/db.php');
include('time-date.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up | GOrder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/signup-form.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
</head>

<body class="">

    <div class="alert alert-no-qty-left bg-danger contact_no_min"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        Please ensure that the contact number entered contains a minimum of 10 digits.
    </div>

    <div class="alert alert-no-qty-left bg-danger age_min"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        You're not allowed to sign up for Gorder. Minimum age requirement is 16 years old.
    </div>

    <div class="alert alert-no-qty-left bg-danger set-up-add"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        Please set up your address to proceed with the registration process.
    </div>

    <div class="alert alert-no-qty-left bg-danger username_min_char"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        Invalid username. Please ensure that the username is 7 characters or more, does not contain special characters, and is not comprised only of numbers.
    </div>

    <div class="alert alert-no-qty-left bg-danger password-format"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        Password needs to have at least 8 characters, including letters, digits, and a special symbol.
    </div>
    
    <div class="alert alert-no-qty-left bg-danger email-exists"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        The email you entered already exists. Please use a different email address.
    </div>
    <div class="alert alert-no-qty-left bg-danger username-exists"">
        <span class="closebtn" onclick="this.parentElement.style.opacity=0;">&times;</span>
        The username you entered already exists. Please choose a different username address.
    </div>

    <form class="signup-container" id="sign-up-form" method="POST" action="process/signup-process.php">
        <div class="create-account-container">
            <h1 class="create-account">Sign up To <em>GOrder</em></h1>
        </div>
        <div class="first-row">
            <p class="cust-info">Customer Information</p>
            <div class="name-container">
                <div class="fname-lname">
                    <div class="input">
                        <input type="text" name="first_name" id="first_name" placeholder="Juan" oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'');" required>
                        <label for="first_name">First Name</label>
                    </div>
                    <div class="input">
                        <input type="text" name="last_name" id="last_name" placeholder="Dela Cruz" oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'');" required>
                        <label for="last_name">Last Name</label>
                    </div>
                </div>

                <div class="mi-suff">
                    <div class="input">
                        <input type="text" name="mi" id="mi" placeholder="A" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');" maxlength="2">
                        <label for="mi">MI</label>
                    </div>
                    <div class="input">
                        <select name="suffix" id="suffix">
                            <option value=""></option>
                            <option value="Sr">Sr</option>
                            <option value="Jr">Jr</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                        </select>
                        <label class="suffix-label" for="suffix">Suffix</label>
                    </div>
                </div>

            </div>

            <div class="other-info-container">
                <div class="bday-sex">
                    <div class="input">
                        <input type="date" name="birthday" id="birthday" required max="<?php echo date('Y-m-d'); ?>">
                        <label for="birthday">Birthday</label>
                    </div>
                    <div class="input">
                        <select name="sex" id="sex">
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                        <label for="sex">Sex</label>
                    </div>
                </div>
                <div class="input">
                    <input type="text" name="contact" id="contact" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                    <label for="contact">Contact No.</label>
                    <div class="contactplus">+63</div>
                    <img src="img/ph-flag.png" alt="PH" class="ph-flag">
                </div>
                <div class="input">
                    <input type="email" name="email" id="email" required>
                    <label for="email">Email</label>
                </div>
            </div>
        </div>

        <div class="second-row">
            <p class="address-label">Address</p>
            <div class="unit">
                <div class="input">
                    <input type="text" name="unit" id="unit" placeholder="Block 1 Lot 1 Marilao Grand Villas" required>
                    <label for="unit">Unit No. / Street / Village</label>
                </div>
            </div>
            <div class="region-province">
                <div class="input">
                    <select name="region" id="region">
                        <option value="" disabled selected>Select Region</option>
                        <?php
                        $region_sql = "SELECT * FROM region WHERE REGION_STATUS = 'active'";
                        $region_result = $conn->query($region_sql);
                        if ($region_result->num_rows > 0) {
                            while ($region = $region_result->fetch_assoc()) {
                        ?>
                                <option value="<?php echo $region['REGION_ID'] ?>"><?php echo $region['REGION'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <label for="region">Region</label>
                </div>
                <div class="input">
                    <select name="province" id="province">
                        <option disabled selected>No Province Set</option>
                    </select>
                    <label for="province">Province</label>
                </div>
            </div>
            <div class="municipality-bgy">
                <div class="input">
                    <select name="municipality" id="municipality">
                        <option disabled selected>No Municipality Set</option>
                    </select>
                    <label for="minicipality">Municipality</label>
                </div>
                <div class="input">
                    <select name="barangay" id="barangay">
                        <option disabled selected>No Barangay Set</option>
                    </select>
                    <label for="barangay">Barangay</label>
                </div>
            </div>
        </div>

        <div class="third-row">
            <p class="account-label">Account</p>
            <div class="account">
                <div class="input">
                    <input type="text" name="username" id="username" required>
                    <label for="username">Username</label>
                </div>
                <div class="input">
                    <input type="password" name="password" id="password" required>
                    <label for="password">Password</label>
                </div>
            </div>
        </div>

        <div class="buttons">
            <a href="index.php" class="btn btn-dark">Cancel</a>
            <input type="submit" name="create" class="btn btn-primary" value="Sign up">
        </div>

    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/sign-up.js"></script>
</body>

</html>