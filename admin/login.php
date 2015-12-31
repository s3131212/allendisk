<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require_once '../config.php';
include '../captcha/simple-php-captcha.php';
if (!session_id()) {
    session_start();
}
$_SESSION['captcha'] = simple_php_captcha();

?>
<!DOCTYPE html>
<html>
<head>
<title>登入管理介面 - <?php echo $config['sitename'];?></title>
<link href="../css/bootstrap.min.css" rel="stylesheet">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<style>body{ background-color: #F8F8F8; }</style>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo $config['sitetitle']; ?> 管理介面</h1>
    <?php 
    if (isset($_GET['err']) && $_GET['err'] == '1') {
        echo '<div class="alert alert-danger"><p>密碼或驗證碼錯誤</p></div>';
    }
    ?>
    <div class="row" style="margin:0 auto;">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body">
                        <form class="form-horizontal" action="loginc.php" method="post" role="form" style="margin:20px">
                            <div class="form-group">
                                <label class="control-label" for="password">密碼</label>
                                <div class="controls">
                                    <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label class="control-label" for="captcha">驗證碼</label>
                                <div class='row'>
                                    <div class='col-md-5 col-md-offset-1'>
                                        <div class="controls">
                                            <input type="text" id="captcha" class="form-control" placeholder="Captcha" name="captcha">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <img src="<?php echo $_SESSION['captcha']['image_src'] ?>" alt="captcha" style='width: 100%; height:auto;' />
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-success btn-block">登入</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <a href="../index.php">← 回首頁</a>
            </div>
        </div>
    <br />
    <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
