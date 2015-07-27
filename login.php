<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
 */
include 'config.php';

if (!session_id()) {
    session_start();
}

session_destroy();
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>登入 -
            <?php
echo $config["sitename"];?>
        </title>
        <meta charset="utf-8" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <style>
        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            background: transparent linear-gradient(to right bottom, #2196F3 0%, #43E3A6 100%) repeat scroll 0% 0%;
            position: relative
        }

        h1,
        p,
        a {
            color: #FFF
        }

        h1 {
            margin: .5em;
            font-weight: 100
        }

        .panel {
            background: rgba(255, 255, 255, .5)
        }

        label {
            color: #333
        }

        .alert {
            margin: 5em 0 1em
        }
        </style>
        <script>
        $(function() {
            $('button').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'loginc.php',
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        name: $('#name').val(),
                        password: $('#password').val()
                    },
                    cache: false,
                    success: function(data) {
                        if (data == 1) {
                            $('.alertcon').empty().html('<div class="alert alert-danger"><p>帳號或密碼有錯誤</p></div>');
                        } else if (data == 2) {
                            $('.alertcon').empty().html('<div class="alert alert-success"><p>登入成功</p></div>');
                            setTimeout(function() {
                                location.href = 'home.php';
                            }, 1000);
                        } else {
                            $('.alertcon').empty().html('<div class="alert alert-danger"><p>系統發生錯誤</p></div>');
                        }
                    }
                });
            });
        });
        </script>
    </head>

    <body>
        <div class="container">
            <div class="col-md-4 col-md-offset-4 alertcon">
                <div class="alert">
                    <p>&nbsp;</p>
                </div>
            </div>
            <div class="col-md-12">
                <h1 class="text-center"><?php
echo $config["sitetitle"];?></h1>
            </div>
            <div class="row" style="margin:0 auto;">
                <div class="col-md-4 col-md-offset-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form class="form-horizontal" action="loginc.php" method="post" role="form" style="margin:20px">
                                <div class="form-group">
                                    <label class="control-label" for="name">帳號</label>
                                    <div class="controls">
                                        <input type="text" id="name" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="password">密碼</label>
                                    <div class="controls">
                                        <input type="password" id="password" class="form-control" name="password">
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
                    <a href="index.php">← 回首頁</a>
                    <?php

if ($config['reg'] == 'true') {?><a href="reg.php" class="pull-right">註冊新帳號 →</a>
                        <?php
}
?>
                </div>
            </div>
            <br />
            <p class="text-center">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
        </div>
    </body>

    </html>
