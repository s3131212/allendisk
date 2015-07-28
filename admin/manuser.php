<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once "../require.php";
_session_start();

if (!$_SESSION["alogin"]) {
    header("Location: login.php");
    exit;
}

$alert = "";

if (isset($_GET["delete"])) {
    foreach ($db->select("file", [
        "owner" => $_GET["delete"]
    ]) as $d) {
        unlink(dirname(dirname(__FILE__)) . "/file/" . $d['realname'] . '.data');
        $db->delete("file", [
            'id' => $d["id"]
        ]);
    }

    foreach ($db
        ->select("dir", [
            "owner" => $_GET["delete"]
        ]) as $d) {
        $db->delete("dir", [
            'id' => $d["id"]
        ]);
    }

    $db->delete("user", [
        'name' => $_GET['delete']
    ]);
    $alert = "<div class='alert alert-success'>刪除成功</div>";
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
        </style>
    </head>

    <body>
        <div class="container">
            <h1 class="text-center"><?php echo $config["sitetitle"];?> 管理介面</h1>
            <ul class="nav nav-tabs">
                <li><a href="index.php">管理介面首頁</a></li>
                <li><a href="setting.php">設定</a></li>
                <li><a href="newuser.php">新增使用者</a></li>
                <li class="active"><a href="#">管理使用者</a></li>
                <li><a href="../index.php">回到首頁</a></li>
                <li><a href="login.php">登出</a></li>
            </ul>
            <?php echo $alert;?>
                <table class="table">
                    <thead>
                        <tr>
                            <td>帳號</td>
                            <td>Email</td>
                            <td>加入時間</td>
                            <td>使用空間</td>
                            <td>管理</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

foreach ($db
    ->select('user') as $d) {
    $used = $db
        ->ExecuteSQL(sprintf("SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = '%s'", $db->SecureData($d['name'])));
    ?>
                            <tr>
                                <td>
                                    <?php echo $d["name"]?>
                                </td>
                                <td>
                                    <?php echo $d["email"]?>
                                </td>
                                <td>
                                    <?php echo $d["jointime"]?>
                                </td>
                                <td>
                                    <?php
echo sizecount(($used[0]["sum"] / 1000 / 1000));?>
                                </td>
                                <td><a href="manuser.php?delete=<?php echo $d["name"];?>" class="btn btn-danger">刪除</td>
                    </tr>
            <?php
}

?>
        </tbody>
    </table>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </body>

    </html>
