<?php
// $baseUrl = 'http://localhost:3000';
  session_start();
  if (isset($_SESSION['user_data'])) {
    header('location:http://localhost:3000/dashboard');
    die();
  }

  if ( strstr($_SERVER['REQUEST_URI'], '/callback?') != '' ) {
    header('location:http://localhost:3000/auth'.str_replace('/callback', '', $_SERVER['REQUEST_URI']));
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codeigniter Oauth Login</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/style.css'); ?>">
</head>
<body>

    <div class="container">
        <a class="btn" href="<?php echo base_url('auth'); ?>">Login with Cashtoken</a>
    </div>
</body>
</html>