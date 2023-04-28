<?php 
$existingEmail = false;
$existingUsername = false;
$shortPassword = false;

if(isset($_POST['create']))
{
    include('database/db.php');

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $mi = $_POST['mi'];
    $suffix = $_POST['suffix'];
 
    $bday = $_POST['birthday'];
    $sex = $_POST['sex'];
 
    $contact_no = $_POST['contact'];
    $email = $_POST['email'];
 
    $unit = $_POST['unit'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];

    $username = $_POST['username']; 
    $password = $_POST['password'];


    $checking_email = "SELECT * FROM customer_user WHERE EMAIL = '$email'";
    $checking_email_result = $conn->query($checking_email);
    
    if($checking_email_result)
    {
        if($checking_email_result->num_rows > 0)
        {
            $existingEmail = true;
        }
        else
        {
            $checking_username = "SELECT * FROM customer_user WHERE USERNAME = '$username'";
            $checking_username_result = $conn->query($checking_username);

            if($checking_username_result->num_rows > 0)
            {
                $existingUsername = true;
            }
            else
            {
                if(strlen($password) < 8)
                {
                    $shortPassword = true;
                }
                else
                {
                    session_start();

                    $_SESSION['authorized'] = true;

                    $_SESSION['fname'] = $fname;
                    $_SESSION['lname'] = $lname;
                    $_SESSION['mi'] = $mi;
                    $_SESSION['suffix'] = $suffix;
                    
                    $_SESSION['bday'] = $bday;
                    $_SESSION['sex'] = $sex;
                    
                    $_SESSION['contact_no'] = $contact_no;
                    $_SESSION['email'] = $email;
                    
                    $_SESSION['unit'] = $unit;
                    $_SESSION['region'] = $region;
                    $_SESSION['province'] = $province;
                    $_SESSION['municipality'] = $municipality;
                    $_SESSION['barangay'] = $barangay;
                    
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;

                    header('Location: process/signup-process.php');
                }
            }
        }
    }
    else
    {
        echo $conn->error;
    }
}



?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign up | GOrder</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
            crossorigin="anonymous">
        <link rel="stylesheet" href="css/signup-form.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
        </style>
        <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
    </head>
    <body class="">
        <?php if($existingEmail == true):?>
            <div class="alert email-exist">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    Existing Email.
            </div>
        <?php elseif($existingUsername == true):?>
            <div class="alert username-exist">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    Existing Username.
            </div>
        <?php elseif($shortPassword == true):?>
            <div class="alert short password">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    Password must be 6 characters.
            </div>
        <?php endif; ?>

        <form class="signup-container" method="post">
            <div class="create-account-container">
                <h1 class="create-account">Sign up To <em>GOrder</em></h1>
            </div>
            <div class="first-row">
                <p class="cust-info">Customer Information</p>
                <div class="name-container">
                    <div class="fname-lname">
                        <div class="input">
                            <input type="text" name="first_name" id="first_name" placeholder="Juan" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');" required>
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="input">
                            <input type="text" name="last_name" id="last_name" placeholder="Dela Cruz" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');" required>
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>

                    <div class="mi-suff">
                        <div class="input">
                            <input type="text" name="mi" id="mi" placeholder="A" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'');"  maxlength="1">
                            <label for="mi">MI</label>
                        </div>
                        <div class="input">
                            <select name="suffix" id="suffix">
                                <option value=""></option>
                                <option value="Jr">Jr</option>
                                <option value="Jr">Jr</option>
                                <option value="Jr">Jr</option>
                            </select>
                            <label class="suffix-label" for="suffix">Suffix</label>
                        </div>
                    </div>

                </div>

                <div class="other-info-container">
                    <div class="bday-sex"> 
                        <div class="input">
                            <input type="date" name="birthday" id="birthday" required>
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
                        <input
                            type="text"
                            name="unit"
                            id="unit"
                            placeholder="Block 1 Lot 1 Marilao Grand Villas" required>
                        <label for="unit">Unit No. / Street / Village</label>
                    </div>
                </div>
                <div class="region-province">
                    <div class="input">
                        <select name="region" id="region">
                            <option value="Sample">Sample</option>
                            <option value="Sample">Sample</option>
                        </select>
                        <label for="region">Region</label>
                    </div>
                    <div class="input">
                        <select name="province" id="province">
                            <option value="Sample">Sample</option>
                            <option value="Sample">Sample</option>
                        </select>
                        <label for="province">Province</label>
                    </div>
                </div>
                <div class="municipality-bgy">
                    <div class="input">
                        <select name="municipality" id="municipality">
                            <option value="Sample">Sample</option>
                            <option value="Sample">Sample</option>
                        </select>
                        <label for="minicipality">Municipality</label>
                    </div>
                    <div class="input">
                        <select name="barangay" id="barangay">
                            <option value="Sample">Sample</option>
                            <option value="Sample">Sample</option>
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
        <script>
            // Get all elements with class="closebtn"
            var close = document.getElementsByClassName("closebtn");
            var i;

            for (i = 0; i < close.length; i++) {
            close[i].onclick = function(){

                var div = this.parentElement;

                div.style.opacity = "0";

                setTimeout(function(){ div.style.display = "none"; }, 600);
            }
            }
        </script>
    </body>
</html>