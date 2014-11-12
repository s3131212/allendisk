<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require('../config.php');  
if(!session_id()) session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
<title>登入管理介面 - <?php echo $config["sitename"];?></title>
<link href="../css/bootstrap.min.css" rel="stylesheet">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<style>body{ background-color: #F8F8F8; }</style>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo $config["sitetitle"]; ?>管理介面</h1>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#">管理介面首頁</a></li>
        <li><a href="../index.php">回到首頁</a></li>
        </ul>
<?php 
$err=$_GET["err"];
if($err=="1"){
  echo '<div class="alert alert-danger"><p>密碼有錯誤</p></div>';
}
?>
<form class="form-horizontal" action="loginc.php" method="post">
    <div class="form-group">
        <label class="control-label" for="password">密碼</label>
        <div class="controls">
            <input type="password" id="password" placeholder="Password" name="password" class="form-control">
        </div>
    </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn">登入</button>
            </div>
        </div>
</form>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>