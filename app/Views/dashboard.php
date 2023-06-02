<?php

if (isset($_SESSION['active']) && (time() - $_SESSION['active'] > 100)) {
    header('location: http://localhost:3000/logout?logout');
    die();
}
$_SESSION['active'] = time();



$data = json_decode($_SESSION['user_data']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codeigniter Oauth Login</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- <a class="btn logout_btn" href="http://localhost:3000/auth.php?logout">Logout</a> -->
    <a class="btn logout_btn" href="<?php echo base_url('logout?logout'); ?>">Logout</a>
    <!-- <a class="btn" href="<?php echo base_url('auth'); ?>">Login with Cashtoken</a> -->

    <div class="container">
        <p class="title"><span class="bold">User ID: </span><span><?= $data->user_id ?></span></p>
        <p class="title"><span class="bold">Username: </span><span><?= $data->username ?></span></p>
        <p class="title"><span class="bold">Display Name: </span><span><?= $data->display_name ?></span></p>
        <p class="title"><span class="bold">First Name: </span><span><?= $data->first_name ?></span></p>
        <p class="title"><span class="bold">Last Name: </span><span><?= $data->last_name ?></span></p>
        <p class="title"><span class="bold">Email: </span><span><?= $data->email ?></span></p>
    </div>

</body>

</html>