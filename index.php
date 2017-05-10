<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
if (!session_id()) {
    session_start();
}
if (!file_exists('install/install.lock')) {
    header('Location: install/index.php');
    exit();
}
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location:home.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $config['sitename'];?></title>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <style>body{ background-color: #F8F8F8; }</style>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo $config['sitetitle']; ?></h1>
        <?php include('nav.php'); ?>
    <div class="jumbotron">
        <h1><?php echo $config['sitename'];?></h1>
        <p><?php echo $config['subtitle'];?></p>
        <?php if ($config['why']) { ?>
            <a href="page.php?id=<?php echo $config['why']; ?>" class="btn btn-primary btn-large">為何使用<?php echo $config['sitename']; ?></a>
        <?php } ?>
    </div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
</html>
