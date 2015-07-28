<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require "../require.php";
_session_start();

if (!$_SESSION["alogin"]) {
    header("Location: login.php");
    exit;
}

global $config;

$used = $db->ExecuteSQL(sprintf("SELECT SUM(`size`) AS `sum` FROM `file`"));
$filecount = $db->ExecuteSQL(sprintf("SELECT COUNT(`id`) AS `count` FROM `file`"));
$sharecount = $db->ExecuteSQL(sprintf("SELECT COUNT(`id`) AS `count` FROM `file` WHERE `share` = '1'"));
$usercount = $db->ExecuteSQL(sprintf("SELECT COUNT(`name`) AS `count` FROM `user`"));

$current_version = '1.5.0';
$newest_version = @file_get_contents('http://ad.allenchou.cc/version.txt');

//$newest_version = '1.5.1'; //development only

if ($newest_version) {
    $updateText = (version_compare($newest_version, $current_version, '>')) ? "<div class=\"alert alert-warning\">新版 Allen Disk 已經發表。請至 <a href=\"http://ad.allenchou.cc\" target=\"_blank\">Allen Disk 官網</a> 下載新版！</div>" : "";
} else {
    $updateText = "<div class=\"alert alert-warning\">查詢升級伺服器失敗。可能是伺服器的暫時性維修或網絡異常。如過了幾天還是失敗，請聯絡 Allen.</div>";
}

if ($_SESSION["alogin"]) {
    $text = [];

    /* ================================================== */
    $text["h1 .container .text-center"] = "{$config['sitetitle']} 管理介面";

    /* ================================================== */
    $text["#1 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"] = "檔案數：<span class='num'>{$filecount[0]['count']} 個</span>";
    $temp = sizecount(($used[0]["sum"] / 1000 / 1000));
    $text["#2 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"] = "佔用空間：<span class='num'>{$temp}</span>";
    $text["#3 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"] = "公開分享檔案數：<span class='num'>{$sharecount[0]['count']} 個</span>";

    /* ================================================== */
    $text["#1 p .container .row .panel .panel-default .col-md-4 .col-md-offset-2 .panel-body"] = "用戶數：<span class='num'>{$usercount[0]['count']} 位</span>";
    $temp = ($config['total'] != 0) ? sizecount($usercount[0]['count'] * $config['total'] / 1000 / 1000) : '無限制';
    $text["#2 p .container .row .panel .panel-default .col-md-4 .col-md-offset-2 .panel-body"] = "總可用空間：<span class='num'>{$temp}</span>";
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>管理員介面 -
            <?php echo $config["sitename"];?>
        </title>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <style>
        body {
            background-color: #F8F8F8;
        }

        p {
            line-height: 30px;
        }

        .num {
            font-size: 20px;
            line-height: 30px;
        }
        </style>
    </head>

    <body>
        <div class="container">
            <h1 class="text-center"><?php echo $text["h1 .container .text-center"];?></h1>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#">管理介面首頁</a></li>
                <li><a href="setting.php">設定</a></li>
                <li><a href="newuser.php">新增使用者</a></li>
                <li><a href="manuser.php">管理使用者</a></li>
                <li><a href="../index.php">回到首頁</a></li>
                <li><a href="login.php">登出</a></li>
            </ul>
            <br />
            <?php echo $updateText;?>
                <div class='row'>
                    <div class="panel panel-default col-md-4 col-md-offset-1">
                        <div class="panel-heading">
                            <h3 class="panel-title">檔案</h3>
                        </div>
                        <div class="panel-body">
                            <p>
                                <?php echo $text["#1 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"];?>
                            </p>
                            <p>
                                <?php echo $text["#2 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"];?>
                            </p>
                            <p>
                                <?php echo $text["#3 p .container .row .panel .panel-default .col-md-4 .col-md-offset-1 .panel-body"];?>
                            </p>
                        </div>
                    </div>
                    <div class="panel panel-default col-md-4 col-md-offset-2">
                        <div class="panel-heading">
                            <h3 class="panel-title">用戶</h3>
                        </div>
                        <div class="panel-body">
                            <p>
                                <?php echo $text["#1 p .container .row .panel .panel-default .col-md-4 .col-md-offset-2 .panel-body"];?>
                            </p>
                            <p>
                                <?php echo $text["#2 p .container .row .panel .panel-default .col-md-4 .col-md-offset-2 .panel-body"];?>
                            </p>
                        </div>
                    </div>
                </div>
        </div>
        </br>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </body>

    </html>
