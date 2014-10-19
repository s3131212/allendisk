<?php include('config.php'); 
if(!session_id()) session_start();
function sizecount($size){
    if ($size>=0.001 &&$size < 1) {
        echo round(($size*1000), 2) . "KB";
    }elseif ($size>=1 &&$size < 1000) {
        echo round($size,2) . "MB";
    }elseif ($size >= 1000) {
        echo round(($size/1000), 2) . 'GB';
    }
}
$res=$db->select("file",array("id"=>$_GET['id']));
if($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) header("Location: rdownfile.php?id=".$_GET["id"]);
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $res[0]["name"]; ?> - <?php echo $config["sitename"];?></title>
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
                <?php if($config["why"]){ ?><li><a href="why.php"><?php echo $config["sitename"];?>的好處</a></li><?php } ?>
                <li><a href="login.php">登入</a></li>
                <?php if($config["reg"]){ ?><li><a href="reg.php">註冊</a></li><?php } ?>
                <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
            </ul>
        <?php } ?>
    <div class="jumbotron">
        <h1><?php echo $res[0]["name"]; ?></h1>
        <p>擁有者：<?php echo $res[0]["owner"]; ?></br>檔案大小：<?php sizecount($res[0]["size"]/1000/1000); ?></br>上傳時間：<?php echo $res[0]["date"]; ?></p>
        <p><a href="rdownfile.php?id=<?php echo $_GET["id"]; ?>" class="btn btn-large btn-primary">下載</a></p>
    </div>
    <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</div>
</body>
</html>