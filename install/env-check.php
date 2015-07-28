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

/* Necessary function */
function check($val) {
    global $error;
    $error = ($val) ? false : true;
    $status = ($error) ? "<span style=\"color:red;\">Χ</span>" : "<span style=\"color:green;\">√</span>";
    echo $status;
}

function check_php_version($version) {
    check(phpversion() >= $version);
}

function check_extension($ext) {
    check(extension_loaded($ext));
}

function nextStep() {
    global $error;
    $status = ($error) ? "<span style=\"color:red;\">您必須解決以上問題才能繼續安裝</span>" : "<a href=\"install-method.php\" class=\"btn btn-primary\">下一步</a>";
    echo $status;
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Allen Disk安裝程序</title>
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
                <li class="active"><a href="#">環境檢查</a></li>
                <li><a href="#">安裝模式</a></li>
                <li><a href="#">MySQL 連線資訊</a></li>
                <li><a href="#">網站設定</a></li>
                <li><a href="#">新增帳號</a></li>
            </ul>
            <h2>安裝環境檢測</h2>
            <table class="table table-hover">
                <tr>
                    <th width="30%">項目</th>
                    <th width="25%">最低配置</th>
                    <th width="25%">最佳配置</th>
                    <th width="20%">檢測結果</th>
                </tr>
                <tr>
                    <td>PHP</td>
                    <td>5.3.7 以上</td>
                    <td>5.5 以上</td>
                    <td>
                        <?php
check_php_version(5.3);?>
                    </td>
                </tr>
                <tr>
                    <td>Multibyte String 函式庫</td>
                    <td>必須支援</td>
                    <td>必須支援</td>
                    <td>
                        <?php
check_extension('mbstring');?>
                    </td>
                </tr>
                <tr>
                    <td>Mysqli 函式庫 </td>
                    <td>必須支援</td>
                    <td>必須支援</td>
                    <td>
                        <?php
check_extension('mysqli');?>
                    </td>
                </tr>
                <tr>
                    <td>Mcrypt 函式庫</td>
                    <td>必須支援</td>
                    <td>必須支援</td>
                    <td>
                        <?php
check_extension('mcrypt');?>
                    </td>
                </tr>
                <tr>
                    <td>GD 函式庫</td>
                    <td>必須支援</td>
                    <td>必須支援</td>
                    <td>
                        <?php
check_extension('gd');?>
                    </td>
                </tr>
            </table>
            <h2>權限檢測</h2>
            <table class="table table-hover">
                <tr>
                    <th width="30%">項目</th>
                    <th width="25%">所需權限</th>
                    <th width="20%">檢測結果</th>
                </tr>
                <tr>
                    <td>database.php</td>
                    <td>可寫</td>
                    <td>
                        <?php
check(is_writable(dirname(dirname(__FILE__)) . '/database.php'));?>
                    </td>
                </tr>
                <tr>
                    <td>database-sample.php</td>
                    <td>可讀</td>
                    <td>
                        <?php
check(is_readable('database-sample.php'));?>
                    </td>
                </tr>
                <tr>
                    <td>install.sql</td>
                    <td>可讀</td>
                    <td>
                        <?php
check(is_writable('install.sql'));?>
                    </td>
                </tr>
                <tr>
                    <td>/file</td>
                    <td>可寫</td>
                    <td>
                        <?php
check(is_writable(dirname(dirname(__FILE__)) . '/file'));?>
                    </td>
                </tr>
            </table>
            <p>
                <?php
nextStep();?>
                    <!--當然你可以用直接瀏覽 install-method.php 的方式來繞過檢測，但是到時無法正常運作是你家的事，我不負責-->
            </p>
        </div>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </body>

    </html>
