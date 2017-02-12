<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if (file_exists('install.lock')) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Allen Disk安裝程序</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <style>body{ background-color: #F8F8F8; }</style>
</head>
<body>
<div class="container">
    <h1 class="text-center">Allen Disk安裝程序</h1>
    <ul class="nav nav-tabs">
        <li><a href="#">環境檢查</a></li>
        <li><a href="#">安裝模式</a></li>
        <li><a href="#">MySQL 連線資訊</a></li>
        <li class="active"><a href="#">網站設定</a></li>
        <li><a href="#">新增帳號</a></li>
    </ul>
    <form method="post" action="site-set.php">
        <table class="table table-hover">
            <thead>
                <tr>
                    <td>名稱</td>
                    <td>值</td>
                    <td>註解</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>網頁標題</td>
                    <td><input type="text" value="" name="sitename" id="sitename" class="form-control" /></td>
                    <td>顯示在&lt;title&gt;，所以別使用HTML Tag</td>
                </tr>
                <tr>
                    <td>網頁名稱</td>
                    <td><input type="text" value="" name="sitetitle" id="sitetitle" class="form-control" /></td>
                    <td>可以利用&lt;img&gt;來顯示Logo</td>
                </tr>
                <tr>
                    <td>上傳單檔大小限制</td>
                    <td><input type="text" value="" name="size" id="size" class="form-control" /></td>
                    <td>單一檔案上傳最大限制，單位為MB，1000MB = 1GB，必須 ≤ upload_max_filesize & post_max_size ， 0 代表無限</td>
                </tr>
                <tr>
                    <td>使用者空間</td>
                    <td><input type="text" value="" name="total" id="total" class="form-control" /></td>
                    <td>單一使用者最大可用空間，單位為MB，1000MB = 1GB ， 0 代表無限</td>
                </tr>
                <tr>
                    <td>網站網址</td>
                    <td><input type="text" value="" name="url" id="url" class="form-control" /></td>
                    <td>填入「首頁網址」而非管理員介面網址，記得加上" http(s):// "和網址最後的" / "</td>
                </tr>
                <tr>
                    <td>標語</td>
                    <td><input type="text" value="" name="subtitle" id="subtitle" class="form-control" /></td>
                    <td>顯示在首頁的標語</td>
                </tr>
                <tr>
                    <td>啟用註冊功能</td>
                    <td><input type="checkbox" name="reg" id="reg" value="true" /></td>
                    <td>允許使用者註冊帳號，個人用網路硬碟請勿勾選</td>
                </tr>        
                <tr>
                    <td>防止 Session 覆蓋</td>
                    <td><input type="checkbox" name="session_protect" id="session_protect" value="true" /></td>
                    <td>防止同一網域底下 Session 互相干擾。若同一網域下沒有其他程式則可以不開啟，若有其他程序，尤其同為 Allen Disk ，則強烈建議開啟此功能。</td>
                </tr>
                <tr>
                    <td>檔案加密</td>
                    <td><input type="checkbox" name="encrypt_file" id="encrypt_file" value="true" /></td>
                    <td>加密上傳的檔案。檔案金鑰為隨機生成，使用 AES 演算法對檔案加密，確保即使遭到入侵，資料也無法被存取。加密會消耗伺服器資源，並增加上傳所需時間，但為了安全性考量，仍強烈建議開啟此功能。注意，開啟與關閉此功能並不會把原先沒有加密過的檔案加密，也不會把已經加密的檔案解密，此選項僅影響完成設定之後的檔案。</td>
                </tr>
                <tr>
                    <td>管理員密碼</td>
                    <td><input type="text" value="" name="admin" id="admin" class="form-control" /></td>
                    <td>到管理介面（/admin）的密碼</td>
                </tr>
            </tbody>
        </table>
    <input type="submit" value="送出" class="btn btn-primary">
    </form>
</div>
<p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
</body>
</html>
