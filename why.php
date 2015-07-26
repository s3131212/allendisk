<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
function sizecount($size){
    if ($size<0.001) {
        return round(($size*1000*1000), 2) . "B";
    }elseif ($size>=0.001 && $size < 1) {
        return round(($size*1000), 2) . "KB";
    }elseif ($size>=1 &&$size < 1000) {
        return round($size, 2) . "MB";
    }elseif ($size >= 1000) {
        return round(($size/1000), 2) . 'GB';
    }
}
$res=$db->select("file",array("id"=>$_GET['id']));
?>
<!DOCTYPE html>
<html>
<head>
<title>為何選用<?php echo $config["sitename"];?></title>
<meta charset="utf-8" />
<link href="css/bootstrap.min.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>body{ background-color: #F8F8F8; }</style>
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo $config["sitetitle"]; ?></h1>
        <?php
        if($_SESSION["login"]){
        ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <li><a href="logout.php">登出</a></li>
            </ul>
        <?php }else{ 
        ?>
            <ul class="nav nav-tabs">
                <li><a href="index.php">首頁</a></li>
                <?php if($config["why"]){ ?><li class="active"><a href="why.php"><?php echo $config["sitename"];?>的好處</a></li><?php } ?>
                <li><a href="login.php">登入</a></li>
                <?php if($config["reg"]){ ?><li><a href="reg.php">註冊</a></li><?php } ?>
                <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
            </ul>
        <?php } ?>
    <div class="well">
        <h2>為何選用<?php echo $config["sitename"];?></h2>
        <ul>
            <li>檔案加密：所有在<?php echo $config["sitename"];?>的檔案都會加密儲存，且每個檔案的密碼皆不一樣，即使伺服器被攻破，也無法解開加密過的檔案</li>
            <li>安全性：<?php echo $config["sitename"];?>內不只是檔案，任何機密資料都會加密儲存，且有對各種方式的攻擊做出防護，可以避免機密外流</li>
            <li>快速：<?php echo $config["sitename"];?>的下載速度是非常快的，相較於中國的免空，<?php echo $config["sitename"];?>的速度快了許多</li>
            <li>不砍檔：要分享檔案還要三不五時補檔嗎？<?php echo $config["sitename"];?>有絕對不砍檔保證（如果違反法律則除外），您的檔案即使不多人下載，也絕對不會被刪除</li>
            <li>支援外連：什麼？免空檔案支援外連是多麼稀少的事情啊！可是，<?php echo $config["sitename"];?>就可以，您的檔案都可以外連，不論音樂影片圖片，甚至HTML,CSS,JS檔案，全部都可以外連，把<?php echo $config["sitename"];?>當作檔案庫也無妨</li>
            <li>下載免等待：幾乎所有免空要下載都要等很久，短至15秒，長至5分鐘，可是<?php echo $config["sitename"];?>完全不用，不論是否為會員，都可以享有直接下載的權利！</li>
            <li>空間：<?php echo $config["sitename"];?>提供<?php echo ($config["total"] != 0) ? "高達".sizecount($config["total"]) : "無限制" ?>的空間，且單檔可以<?php echo ($config["size"] != 0) ? "高達".sizecount($config["size"]) : "無限制大小" ?></p></li>
        </ul>
    </div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
</html>