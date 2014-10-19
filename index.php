<?php
include('config.php'); 
if(!session_id()) session_start();
if(!file_exists("install/install.lock")){
    header("Location: install/index.php");
    exit();
}
if($_SESSION["login"]){
    header("Location:home.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $config["sitename"];?></title>
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
    <h1 class="text-center"><?php echo $config["sitetitle"]; ?></h1>
        <?php
        if($_SESSION["login"]){
        ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <li><a href="logout.php">登出</a></li>
            </ul>
        <?php }else{ 
        ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <?php if($config["why"]){ ?><li><a href="why.php"><?php echo $config["sitename"];?>的好處</a></li><?php } ?>
                <li><a href="login.php">登入</a></li>
                <?php if($config["reg"]){ ?><li><a href="reg.php">註冊</a></li><?php } ?>
                <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
            </ul>
        <?php } ?>
    <div class="jumbotron">
    <h1><?php echo $config["sitename"];?></h1>
    <p>最先進，最大方，最安全的網路硬碟</p>
    <p>
        <?php if($config["why"]){ ?><a href="why.php" class="btn btn-primary btn-large">為何使用<?php echo $config["sitename"];?></a><?php } ?>
    </p>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
</html>