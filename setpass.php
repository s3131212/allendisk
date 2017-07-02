<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
include dirname(__FILE__).'/class/password_compat.php';
if (!session_id()) {
    session_start();
}
if (!$_SESSION['login']) {
    exit();
}

function md5_128($text)
{
    for ($i = 0; $i < 128; ++$i) {
        $text = md5($text);
    }

    return $text;
}

$re = 0;
if (isset($_POST['pass']) && isset($_POST['pass2'])) {
    //Check CSRF
    if($_POST['csrf_token2'] != $_SESSION['csrf_token'][$_POST['csrf_token1']]){
        die('Token error');
    }else{
         unset($_SESSION['csrf_token'][$_POST['csrf_token1']]);
    }

    if ($_POST['pass'] != $_POST['pass2']) {
        $re = 1;
    } else {
        if ($_POST['pass'] != null) {
            $db->update('user', array('pass' => password_hash($_POST['pass'], PASSWORD_DEFAULT)), array('name' => $_SESSION['username']));
            $new_password = md5_128($_POST['pass']);
            foreach ($db->select('file', array('owner' => $_SESSION['username'])) as $value) {
                /* Change Key */
                /* Get original key */
                $passphrase['b'] = $_SESSION['password'];
                $passphrase['c'] = base64_decode($value['secret']);
                $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
                $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
                $passphrase['a'] = openssl_decrypt($passphrase['c'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

                /* Update new key */
                $passphrase['b'] = $new_password;
                $iv = substr(md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true), 0 ,16);
                $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 32);
                $passphrase['c'] = openssl_encrypt($passphrase['a'], 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

                $db->update('file', array('secret' => base64_encode($passphrase['c'])), array('id' => $value['id']));
            }
            $re = 2;
        }
    }
}

//Generate CSRF Token
$csrf_token_id = sha1(md5(mt_rand().uniqid()));
$_SESSION['csrf_token'][$csrf_token_id] = sha1(md5(mt_rand().uniqid()));

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function redirection(){
            setTimeout(function(){
                window.parent.location.href = "logout.php";
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="repsd">
    <?php if ($re == 1) {
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
            <input type="hidden" name="csrf_token1" value="<?php echo $csrf_token_id ?>" />
            <input type="hidden" name="csrf_token2" value="<?php echo $_SESSION['csrf_token'][$csrf_token_id] ?>" />
            <input type="submit" value="送出" class="btn btn-info"/>
        </form>
    </div>
</body>
</html>
