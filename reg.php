<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
include 'config.php';
include "captcha/simple-php-captcha.php";
include '../class/password_compat.php';

if (!session_id()) {
    session_start();
}

if (isset($_POST["name"]) && isset($_POST["password2"]) && isset($_POST["password"]) && $config["reg"] == 'true') {
    $username = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $namecheck = $db
        ->ExecuteSQL(sprintf("SELECT count(*) AS `count` FROM `user` WHERE `name` = '%s'", $db->SecureData($username)));

    if ($namecheck[0]["count"] > 0) {
        $err = 2;
    } elseif ($username == "") {
        $err = 0;
    } elseif ($email == "") {
        $err = 0;
    } elseif ($password == "") {
        $err = 0;
    } elseif ($password != $password2) {
        $err = 1;
    } elseif (strtolower($_POST["captcha"]) != strtolower($_SESSION['captcha']['code'])) {
        $err = 4;
    } else {
        $db->insert([
            "name"  => $username,
            "pass"  => password_hash($password, PASSWORD_DEFAULT),
            "email" => $email
        ], "user");
        $err = 3;
    }
}

$_SESSION['captcha'] = simple_php_captcha();
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>註冊 -
            <?php
echo $config["sitename"];?>
        </title>
        <meta charset="utf-8" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
        body {
            background-color: #F8F8F8;
        }
        </style>
        <script src="js/bootstrap.min.js"></script>
    </head>

    <body>
        <div class="container">
            <h1 class="text-center">Allen Disk</h1>
            <div class="col-md-6 col-md-offset-3">
                <?php

if ($_SESSION["login"]) {?>
                    <ul class="nav nav-tabs">
                        <li><a href="index.php">首頁</a>
                        </li>
                        <li><a href="logout.php">登出</a>
                        </li>
                    </ul>
                    <?php
} else {
    ?>
                        <ul class="nav nav-tabs">
                            <li><a href="index.php">首頁</a>
                            </li>
                            <?php

    if ($config["why"]) {?>
                                <li>
                                    <a href="why.php">
                                        <?php
echo $config["sitename"];?>的好處</a>
                                </li>
                                <?php
}

    ?>
                                    <li><a href="login.php">登入</a>
                                    </li>
                                    <?php

    if ($config["reg"]) {?>
                                        <li class="active"><a href="reg.php">註冊</a>
                                        </li>
                                        <?php
}

    ?>
                                            <?php

    if ($config["tos"]) {?>
                                                <li><a href="tos.php">使用條款</a>
                                                </li>
                                                <?php
}

    ?>
                        </ul>
                        <?php
}

?>
                            <?php

if ($config["reg"]) {
    ?>
                                <?php

    if ($err == "1") {
        echo '<div class="alert alert-danger">兩次輸入的密碼必須相同</div>';
    }

    if ($err == "0") {
        echo '<div class="alert alert-danger">不能有任何欄位是空白的</div>';
    } elseif ($err == "2") {
        echo '<div class="alert alert-danger">已經有重複的帳號</div>';
    } elseif ($err == "3") {
        echo '<div class="alert alert-success">註冊完成</div>';
    } elseif ($err == "4") {
        echo '<div class="alert alert-danger">驗證碼錯誤</div>';
    }

    ?>
                                    <br/>
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <form action="reg.php" method="post" role="form">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label" for="name">帳號</label>
                                                            <div class="controls">
                                                                <input type="text" id="name" class="form-control" placeholder="Username" name="name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label" for="email">電子郵件</label>
                                                            <div class="controls">
                                                                <input type="text" id="email" placeholder="Email" name="email" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label" for="password">密碼</label>
                                                            <div class="controls">
                                                                <input type="password" id="password" placeholder="Password" name="password" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label" for="password2">重新輸入密碼</label>
                                                            <div class="controls">
                                                                <input type="password" id="password2" placeholder="Password again" name="password2" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label" for="captcha">驗證碼（不分大小寫）</label>
                                                            <div class="controls">
                                                                <input type="text" id="captcha" placeholder="Captcha" name="captcha" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <img src="<?php
echo $_SESSION['captcha']['image_src']?>" alt="captcha" />
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <br/>
                                                        <p>註冊後代表您已經同意<a href="tos.php">使用條款</a>且如果有違反，
                                                            <?php
echo $config["sitename"];?>將不必負任何責任</p>
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <button type="submit" class="btn btn-primary">註冊</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <?php
} else {
    echo "<div class=\"alert alert-warning\" role=\"alert\">很抱歉，" . $config["sitename"] . "已經關閉註冊</div>";
}

?>
            </div>
            <p class="text-center text-info col-md-12">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a>
            </p>
        </div>
    </body>

    </html>
