<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
 */
require "../require.php";
_session_start();

if (!$_SESSION["alogin"]) {
    header("Location: login.php");
    exit;
}

$title = "{$config["sitetitle"]} 管理介面";

$successMsg = (isset($_GET["s"]) && $_GET["s"] == "1"):
"<div class=\"alert alert-success\">新增完成</div>":
"";

if (isset($_GET["err"])) {
    switch ($_GET["err"]) {
        case "0":
            $alertInfo = "所有欄位必填";
            break;

        case "2":
            $alertInfo = "已經有重複的帳號";
            break;

        default:
            $alertInfo = "未知錯誤";
            break;
    }
}

$alertMsg = (isset($alertInfo)):
"<div class=\"alert alert-danger\">{$alertInfo}</div>":
"";
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>管理員介面 -
            <?php
echo $config["sitename"];?>
        </title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <style>
        body {
            background-color: #F8F8F8;
        }
        </style>
    </head>
    <?php

if ($_SESSION["alogin"]) {?>

        <body>
            <div class="container">
                <h1 class="text-center"><?php
echo $title;?></h1>
                <ul class="nav nav-tabs">
                    <li><a href="index.php">管理介面首頁</a></li>
                    <li><a href="setting.php">設定</a></li>
                    <li class="active"><a href="newuser.php">新增使用者</a></li>
                    <li><a href="manuser.php">管理使用者</a></li>
                    <li><a href="../index.php">回到首頁</a></li>
                    <li><a href="login.php">登出</a></li>
                </ul>
                <?php
echo $successMsg . $alertMsg;?>
                    <p style="font-size:30px;">新增使用者</p>
                    <div class="row" style="margin:0 auto;">
                        <div class="col-md-6">
                            <form class="form-horizontal" action="newb.php" method="post">
                                <div class="form-group">
                                    <label class="control-label" for="username">帳號</label>
                                    <div class="controls">
                                        <input type="text" id="username" class="form-control" placeholder="Username" name="username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="password">密碼</label>
                                    <div class="controls">
                                        <input type="text" id="password" class="form-control" placeholder="Password" name="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="email">Email</label>
                                    <div class="controls">
                                        <input type="text" id="email" class="form-control" placeholder="Email" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <button type="submit" class="btn btn-default">新增使用者</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    </br>
            </div>
            <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
        </body>

    </html>
