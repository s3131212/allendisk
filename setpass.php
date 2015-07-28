<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

if (!session_id()) {
    session_start();
}

if (!$_SESSION['login']) {
    exit;
}

include 'config.php';
include dirname(__FILE__ . '/class/password_compat.php');

function md5_128($text) {

    for ($i = 0; $i < 128; ++$i) {
        $text = md5($text);
    }

    return $text;
}

$re = 0;

if (isset($_POST['pass']) && isset($_POST['pass2'])) {
    if ($_POST['pass'] != $_POST['pass2']) {
        $re = 1;
    } else {

        if ($_POST['pass'] != null) {
            $db->update('user', [
                'pass' => password_hash($_POST['pass'], PASSWORD_DEFAULT)
            ], [
                'name' => $_SESSION['username']
            ]);
            $new_password = md5_128($_POST['pass']);

            foreach ($db->select("file", [
                "owner" => $_SESSION['username']
            ]) as $value) {
                /* Change Key */
                /* Get original key */
                $passphrase['b'] = $_SESSION['password'];
                $passphrase['c'] = $value['secret'];
                $iv = substr(md5("\x1B\x3C\x58" . $passphrase['b'], true), 0, 8);
                $key = substr(md5("\x2D\xFC\xD8" . $passphrase['b'], true) . md5("\x2D\xFC\xD9" . $passphrase['b'], true), 0, 24);
                $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
                /* Update new key */
                $passphrase['b'] = $new_password;
                $iv = substr(md5("\x1B\x3C\x58" . $passphrase['b'], true), 0, 8);
                $key = substr(md5("\x2D\xFC\xD8" . $passphrase['b'], true) . md5("\x2D\xFC\xD9" . $passphrase['b'], true), 0, 24);
                $passphrase['c'] = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $passphrase['a'], MCRYPT_MODE_CBC, $iv)), "\0\3");
                $db->update('file', [
                    'secret' => $passphrase['c']
                ], [
                    'id' => $value['id']
                ]);
            }

            $re = 2;
        }
    }
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js/bootstrap.min.js"></script>
        <script>
        function redirection() {
            setTimeout(function() {
                window.parent.location.href = "logout.php";
            }, 3000);
        }
        </script>
    </head>

    <body>
        <div class="repsd">
            <?php

if ($re == 1) {
    echo '<div class="alert alert-warning" role="alert">兩次密碼不相同，請重新輸入</div>';
} elseif ($re == 2) {
    echo '<div class="alert alert-success" role="alert">變更完成，三秒後自動登出，請重新登入</div><script>redirection();</script>';
}

?>
                <form action="setpass.php" method="post" role="form">
                    <div class="form-group">
                        <input type="password" name="pass" id="pass" placeholder="密碼" class="form-control" required/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="pass2" id="pass2" placeholder="確認密碼" class="form-control" required/>
                    </div>
                    <input type="submit" value="送出" class="btn btn-info" />
                </form>
        </div>
    </body>

    </html>
