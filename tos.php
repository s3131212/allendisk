<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();?>
<!DOCTYPE html>
<html>
<head>
<title>使用者條款 - <?php echo $config["sitename"];?></title>
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
                <?php if($config["tos"]){ ?><li class="active"><a href="tos.php">使用條款</a></li><?php } ?>
            </ul>
        <?php } ?>
        <div class="well">
            <h2><?php echo $config["sitename"];?>使用條款</h2>
            <ol>
                <li>您所上傳到<?php echo $config["sitename"];?>的所有檔案、文件、資料等（以下簡稱「檔案」），不能違反中華民國、美國之法律，若違反了中華民國或美國法律，<?php echo $config["sitename"];?>會無條件將檔案提供給「可靠」的第三方供調查或進行法律裁定，且<?php echo $config["sitename"];?>不負任何責任。</li>
                <li>您的檔案，<?php echo $config["sitename"];?>有義務幫您備份，但若發生檔案遺失損毀或其他狀況，<?php echo $config["sitename"];?>會盡力幫您恢復，若無法取回檔案，<?php echo $config["sitename"];?>不負任何責任。</li>
                <li>您的檔案，<?php echo $config["sitename"];?>保證不會主動提供給第三方，但如果遇到特殊狀況，如違反第一項，<?php echo $config["sitename"];?>會主動或被動且無條件提供給「可靠」的第三方（如政府機構），若遇到特殊狀況，如主機遭到入侵、破壞、竊聽，或<?php echo $config["sitename"];?>系統有漏洞，導致您的個人資料外露，<?php echo $config["sitename"];?>僅能盡力封鎖該問題或漏洞，對於已外洩的個資，<?php echo $config["sitename"];?>不負任何責任。</li>
                 <li>您的個人資料，<?php echo $config["sitename"];?>保證不會主動或被動提供給第三方，但如果遇到特殊狀況，如違反第一項，<?php echo $config["sitename"];?>會主動或被動且無條件提供給「可靠」的第三方（如政府機構），若遇到特殊狀況，如主機遭到入侵、破壞、竊聽，或<?php echo $config["sitename"];?>系統有漏洞，導致您的個人資料外露，<?php echo $config["sitename"];?>僅會盡力封鎖該問題或漏洞，對於已遺失或外露的檔案，<?php echo $config["sitename"];?>不負任何責任。</li>
                <li>您的檔案不可包含有色情、猥褻、暴力、血腥、政治偏見、種族歧視、盜版內容、駭客/黑客軟體、破解軟體、非法活動資訊等內容，如果被檢舉或遭<?php echo $config["sitename"];?>發現，該檔案將被刪除，並將您的帳戶列入觀察名單，屢次發現違反規定，您的帳戶將被無條件且無通知移除。若因此違反第一條規定，<?php echo $config["sitename"];?>將主動或被動且無條件把資訊提供給第三方。</li>
                <li><?php echo $config["sitename"];?>有權將檔案及帳戶有條件或無條件刪除（但通常不會這麼做），且不必告知，不一定要有理由（通常<?php echo $config["sitename"];?>不會無理由且無通知主動刪除帳號）</li>
                <li><?php echo $config["sitename"];?>有權更動此條款，且不必向使用者通知。</li>
                <li><?php echo $config["sitename"];?>若因財務、資安、法律等，及其他特殊理由，可關閉此服務，您可以要求在關閉後將檔案歸還給您，但<?php echo $config["sitename"];?>不保證若因為特殊原因，導致檔案「全數」遺失的問題不會發生，一旦發生，<?php echo $config["sitename"];?>會「無法」將檔案還給您。</li>
                <li><?php echo $config["sitename"];?>不強迫您在設定您的Email，不過如果您疑似違規了，而您有填寫Email，<?php echo $config["sitename"];?>可以先透過Email請您解釋或提出您沒有違規的證明，否則我們將直接將檔案刪除</li>
                <li>若您註冊了本服務，代表您已經「完全同意」以上條款，且願意遵守。</li>
            </ol>
        </div>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
</body>
</html>
