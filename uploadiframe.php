<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <form action="uploadfileiframe.php" enctype="multipart/form-data" method="post">
        <input id="file" name="file[]" type="file" multiple required></br>
        <input id="submit" name="submit" type="submit" class="btn btn-primary" value="開始上傳">
        <a href="diruploadiframe.php" class="btn btn-link">上傳資料夾</a>
    </form>
</body>
</html>