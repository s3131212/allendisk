<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
if (!session_id()) {
    session_start();
}
if(!isset($_GET['id'])){
    header('Location: index.php');
    exit();
}
function sizecount($size){
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

$res = $db->select('page', array('id' => $_GET['id']));

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo replace_attr($res[0]['title']); ?> - <?php echo $config['sitename'];?></title>
<meta charset="utf-8" />
<link href="css/bootstrap.min.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>body{ background-color: #F8F8F8; }</style>
<script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
    	<h1 class="text-center"><?php echo $config['sitetitle']; ?></h1>
        <?php include('nav.php'); ?>
        <div class="well">
            <h2><?php echo replace_attr($res[0]['title']); ?></h2>
            <div>
                <?php echo replace_attr($res[0]['context']); ?>
            </div>
        </div>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
</body>
</html>
