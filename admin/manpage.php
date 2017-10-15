<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
require '../config.php';
if (!session_id()) {
    session_start();
}
if (!$_SESSION['alogin']) {
    header('location:login.php');
    exit();
}

function sizecount($size)
{
    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2).'MB';
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2).'GB';
    }
}

function replace_attr($context){
    global $config;
    $context = str_replace("{sitename}", $config['sitename'], $context);
    $context = str_replace("{size}", sizecount($config['size']), $context);
    $context = str_replace("{url}", sizecount($config['url']), $context);
    $context = str_replace("{total}", $config['total'], $context);
    $context = str_replace("{subtitle}", $config['subtitle'], $context);
    return $context;
}

if(isset($_GET['id'])){
    $res = $db->select('page', array('id' => $_GET['id']));
    $insertmode = false;
}else{
    $insertmode = true;
}

$alert = '';
if (isset($_GET['delete'])) {
    //Check CSRF
    if($_GET['csrf_token2'] != $_SESSION['csrf_token'][$_GET['csrf_token1']]){
        die('Token error');
    }else{
         unset($_SESSION['csrf_token'][$_GET['csrf_token1']]);
    }
    $db->delete('page', array('id' => $_GET['id']));
    header('Location: page.php?success=delete');
    exit();
}
if(isset($_POST['context'])){
    
    //Check CSRF
    if($_POST['csrf_token2'] != $_SESSION['csrf_token'][$_POST['csrf_token1']]){
        die('Token error');
    }else{
         unset($_SESSION['csrf_token'][$_POST['csrf_token1']]);
    }

    if($_POST['id'] == 'new'){
        $db->ExecuteSQL(sprintf("INSERT INTO `page` (`id`, `title`, `context`) VALUES (NULL, '%s', '%s');", $db->SecureData($_POST['title']), $db->SecureData($_POST['context'])));
    }else{
        $db->ExecuteSQL(sprintf("UPDATE `page` SET `title` = '%s', `context` = '%s' WHERE `page`.`id` = '%s';", $db->SecureData($_POST['title']), $db->SecureData($_POST['context']),$db->SecureData($_POST['id'])));
    }
    header('Location: page.php?success=edit');
    exit();
}

//Generate CSRF Token
$csrf_token_id = sha1(md5(mt_rand().uniqid()));
$_SESSION['csrf_token'][$csrf_token_id] = sha1(md5(mt_rand().uniqid()));

?>
<!DOCTYPE html>
<html>
<head>
    <title>管理員介面 - <?php echo $config['sitename'];?></title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <link href="../css/summernote/summernote.css" rel="stylesheet">
    <script src="../js/summernote.min.js"></script>
    <script src="../js/summernote-zh-TW.js"></script>
    <style>body{ background-color: #F8F8F8; }</style>
    <script>
        $(document).ready(function() {
            $('#context').val($('#texteditor').html());
            $('#texteditor').summernote({
                lang: 'zh-TW'
            });
            $('#texteditor').on('summernote.change', function(we, contents, $editable) {
                console.log('summernote\'s content is changed.');
                $('#context').val(contents);
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center"><?php echo $config['sitetitle']; ?> 管理介面</h1>
        <ul class="nav nav-tabs">
            <li><a href="index.php">管理介面首頁</a></li>
            <li><a href="setting.php">設定</a></li>
            <li><a href="newuser.php">新增使用者</a></li>
            <li><a href="manuser.php">管理使用者</a></li>
            <li class="active"><a href="page.php">頁面</a></li>
            <li><a href="../index.php">回到首頁</a></li>
            <li><a href="login.php">登出</a></li>
        </ul>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" id="title" name="title" class="form-control" placeholder="標題" value="<?php echo ($insertmode) ? '' : $res[0]['title']; ?>" />
            </div>
            <div id="texteditor"><?php echo ($insertmode) ? '' : $res[0]['context']; ?></div>
            <input type="hidden" id="context" name="context" />
            <input type="hidden" name="csrf_token1" value="<?php echo $csrf_token_id ?>" />
            <input type="hidden" name="csrf_token2" value="<?php echo $_SESSION['csrf_token'][$csrf_token_id] ?>" />
            <input value="<?php echo ($insertmode) ? 'new' : $_GET['id']; ?>" type="hidden" id="id" name="id" />
            <input class="btn btn-primary" value="送出" type="submit" />
            <a class="btn btn-link" href="manpage.php?id=<?php echo $_GET['id']; ?>&delete=true&csrf_token1=<?php echo $csrf_token_id ?>&csrf_token2=<?php echo $_SESSION['csrf_token'][$csrf_token_id] ?>">刪除</a>
        </form>
    </div>
    <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>
