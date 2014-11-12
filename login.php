<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php');  
if(!session_id()) session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
<title>登入 - <?php echo $config["sitename"];?></title>
<meta charset="utf-8" />
<link href="css/bootstrap.min.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>body{ background-color: #F8F8F8; }</style>
<script src="js/bootstrap.min.js"></script>
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
                <li class="active"><a href="login.php">登入</a></li>
                <?php if($config["reg"]){ ?><li><a href="reg.php">註冊</a></li><?php } ?>
                <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
            </ul>
        <?php } ?>
    <?php 
    $err=$_GET["err"];
    if($err=="1"){
      echo '<div class="alert alert-danger"><p>帳號或是密碼有錯誤</p></div>';
    }
    if($err=="0"){
      echo '<div class="alert alert-danger"><p>發生不明錯誤</p></div>';
    }
    ?>
    <div class="row" style="margin:0 auto;">
        <div class="col-md-4" >
            <form class="form-horizontal" action="loginc.php" method="post" role="form">
                <div class="form-group">
                    <label class="control-label" for="name">帳號</label>
                    <div class="controls">
                      <input type="text" id="name" class="form-control" placeholder="帳號" name="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">密碼</label>
                    <div class="controls">
                        <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="controls">
                        <button type="submit" class="btn">登入</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
</html>