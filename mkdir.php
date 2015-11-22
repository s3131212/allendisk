<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if (!session_id()) {
    session_start();
}
if (!$_SESSION['login']) {
    exit();
}
include 'config.php';
$re = 0;
if (isset($_POST['name']) && $_POST['name'] != null) {
    $name = str_replace('/', '', $_POST['name']);
    $dircheck = $db->select('dir', array('owner' => $_SESSION['username'], 'name' => $name, 'parent' => $_SESSION['dir']));
    if ($dircheck[0]['id'] != null) {
        $re = 1;
    } else {
        $db->insert(array('id' => sha1(md5(mt_rand().uniqid())), 'name' => $name, 'owner' => $_SESSION['username'], 'parent' => $_SESSION['dir'], 'recycle' => '0'), 'dir');
        $re = 2;
        $token = @touch(dirname(__FILE__).'/updatetoken/'.md5($_SESSION['username']).'.token');
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
</head>
<body>
    <div class="repsd">
    <?php if ($re == 1) {
    echo '<div class="alert alert-warning" role="alert">此目錄下有重複名稱的資料夾</div>';
} elseif ($re == 2) {
        echo '<div class="alert alert-success" role="alert">新增完成</div>';
    }
    ?>
        <form action="mkdir.php" method="post" role="form">
            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="資料夾名稱" class="form-control" required />
            </div>
            <input type="submit" value="送出" class="btn btn-info"/>
        </form>
    </div>
</body>
</html>
