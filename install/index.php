<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Allen Disk安裝程序</title>
<link href="../css/bootstrap.min.css" rel="stylesheet">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<style>body{ background-color: #F8F8F8; }</style>
<?php if(isset($_GET["fin"])&&$_GET["fin"] == "fin") echo '<meta http-equiv="refresh" content="3; url=../index.php">'; ?>
</head>
<body>
<div class="container">
    <h1 class="text-center">Allen Disk安裝程序</h1>
    <ul class="nav nav-tabs">
            <li><a href="#">環境檢查</a></li>
            <li><a href="#">MySQL 連線資訊</a></li>
            <li><a href="#">網站設定</a></li>
            <li><a href="#">新增帳號</a></li>
    </ul>
<?php
if(file_exists("install.lock")&&!isset($_GET["fin"])){?>
    <div class="alert alert-warning" role="alert">很抱歉，Allen Disk已經安裝完成，如果要重新進行安裝，請刪除 /install/install.lock</div>
<?php }elseif(isset($_GET["fin"])&&$_GET["fin"] == "fin"){ ?>
    <div class="alert alert-success" role="alert">恭喜！Allen Disk已經安裝完成，您即將被導引至首頁</div>
<?php }elseif(!file_exists("install.lock")&&!isset($_GET["fin"])){ ?>
    <script>window.location.href = 'env-check.php';</script>
<?php } ?>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>