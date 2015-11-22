<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if (file_exists('install.lock')) {
    header('Location: ../index.php');
    exit();
}
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
</head>
<body>
<div class="container">
    <h1 class="text-center">Allen Disk安裝程序</h1>
    <ul class="nav nav-tabs">
        <li><a href="#">環境檢查</a></li>
        <li><a href="#">安裝模式</a></li>
        <li><a href="#">MySQL 連線資訊</a></li>
        <li><a href="#">網站設定</a></li>
        <li class="active"><a href="#">新增帳號</a></li>
    </ul>
    <?php 
    $err = $_GET['err'];
    if ($_GET['err'] == '0') {
        echo '<div class="alert alert-danger">不能有任何欄位是空白的</div>';
    }
    ?>
    <form method="post" action="newuser-set.php">
        <div class="form-group">
            <label for="username">帳號</label>
            <input type="text" class="form-control" id="username" placeholder="帳號" name="username">
        </div>
        <div class="form-group">
            <label for="password">密碼</label>
            <input type="text" class="form-control" id="password" placeholder="密碼" name="password">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Email" name="email">
        </div>
    <input type="submit" value="送出" class="btn btn-primary">
    </form>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>