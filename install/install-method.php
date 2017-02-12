<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
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
        <li class="active"><a href="#">安裝模式</a></li>
        <li><a href="#">MySQL 連線資訊</a></li>
        <li><a href="#">網站設定</a></li>
        <li><a href="#">新增帳號</a></li>
    </ul>
    <br />
    <div>
        <a href="mysql-setting.php" class="btn btn-primary btn-lg btn-block">全新安裝</a>
        <p>由於技術問題，目前不提供升級程序。如果您已經有 1.6 版且正常運作，請所有用戶下載檔案之後安裝全新的 1.7 並重新上傳檔案。</p>
    </div>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>
