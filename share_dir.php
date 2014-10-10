<?php
include('config.php'); 
if(!session_id()) session_start();
$dir = $db->select("dir",array('id' => $_GET["id"]));
$dircheck=$db->select("dir",array('owner' => $dir[0]["owner"],'parent'=>$_GET["id"]));
$filecheck=$db->select("file",array('owner' => $dir[0]["owner"],'dir'=>$_GET["id"]));
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
    <div class="container">
        <h1 class="text-center"><?php echo $config["sitetitle"]; ?></h1>
        <ul class="nav nav-tabs">
            <li><a href="index.php">首頁</a></li>
            <?php if($config["why"]){ ?><li><a href="why.php"><?php echo $config["sitename"];?>的好處</a></li><?php } ?>
            <li><a href="login.php">登入</a></li>
            <?php if($config["reg"]){ ?><li><a href="reg.php">註冊</a></li><?php } ?>
            <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
        </ul>
        <h2>檢視<?php echo $dir[0]["name"] ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <td>檔名</td>
                    <td>檔案類型</td>
                    <td>動作</td>
                </tr>
            </thead>
            <tbody>
            <?php if($dircheck[0]["id"]!=NULL){ 
                foreach($dircheck as $d){ ?>
                    <tr>
                        <td><?php echo $d['name']; ?></td>
                        <td>資料夾</td>
                        <td><a href="share_dir.php?id=<?php echo $d["id"]; ?>" class="btn btn-default">開啟</a></td>
                    </tr>
            <?php }
            } if($filecheck[0]["id"]!=NULL){
                foreach($filecheck as $d){ ?>
                    <tr>
                        <td><?php echo $d['name']; ?></td>
                        <td><?php echo $d['type']; ?></td>
                        <td><a href="downfile.php?id=<?php echo $d['id']; ?>" target="_blank" class="btn btn-default">下載</a></td>
                    </tr>
            <?php } }?>
            </tbody>
        </table>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
</body>
</html>