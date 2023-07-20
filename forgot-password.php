<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/forgot-password.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,900;1,200;1,500&family=Roboto+Condensed:wght@300;400&display=swap');
    </style>
    <link rel="shortcut icon" href="img/ggd-logo-plain.png" type="image/x-icon">
</head>

<body>
    <div class="main-container">
        <center>Forgot Password?</center>
        <span id="email-checking" class=""></span>
        <div class="input-email-container container">
            <select id="account-type" class="form-control">
                <option value="customer">Customer</option>
                <option value="emp">Employee</option>
            </select>
            <label>Account Type</label>
        </div>
        <div class="input-email-container container">
            <input type="email" id="email" placeholder="example@example.com" class="form-control">
            <label>Input your Email</label>
        </div>
        <input type="submit" id="submit-email" class="btn-submit-email btn btn-primary" disabled>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/forgot-password.js"></script>
</body>

</html>