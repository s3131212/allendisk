<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

if (file_exists("install.lock")) {
    header("Location: ../index.php");
    exit;
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Allen Disk 安裝程序</title>
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

    <body>
        <div class="container">
            <h1 class="text-center">Allen Disk 安裝程序</h1>
            <ul class="nav nav-tabs">
                <li><a href="#">環境檢查</a></li>
                <li><a href="#">安裝模式</a></li>
                <li class="active"><a href="#">MySQL 連線資訊</a></li>
                <li><a href="#">網站設定</a></li>
                <li><a href="#">新增帳號</a></li>
            </ul>
            <form method="post" action="mysql-set.php">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <td>名稱</td>
                            <td>值</td>
                            <td>註解</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MySQL 伺服器</td>
                            <td>
                                <input type="text" value="localhost" name="host" id="host" class="form-control" />
                            </td>
                            <td>MySQL伺服器網址 / IP ，本機可用 localhost</td>
                        </tr>
                        <tr>
                            <td>MySQL 使用者名稱</td>
                            <td>
                                <input type="text" value="" name="username" id="username" class="form-control" />
                            </td>
                            <td>MySQL 使用者名稱，基於安全性請盡量不要使用 root</td>
                        </tr>
                        <tr>
                            <td>MySQL 使用者密碼</td>
                            <td>
                                <input type="text" value="" name="password" id="password" class="form-control" />
                            </td>
                            <td>MySQL 使用者密碼</td>
                        </tr>
                        <tr>
                            <td>MySQL 資料庫名稱</td>
                            <td>
                                <input type="text" value="" name="dbname" id="dbname" class="form-control" />
                            </td>
                            <td>MySQL 資料庫名稱，請先新增好此資料庫，安裝程式會自動為您建構資料庫結構</td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" value="送出" class="btn btn-primary">
            </form>
        </div>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </body>

    </html>
