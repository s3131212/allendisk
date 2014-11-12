<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
$res=$db->select("file",array("id"=>$_GET['id'],"recycle"=>"0"));
if(!$_SESSION["login"] || $_SESSION["username"] != $res[0]["owner"]){
    if($_SERVER["HTTP_REFERER"]!=$config["url"]."downfile.php?id=".$_GET['id']){
        header("Location: ".$config["url"]."downfile.php?id=".$_GET['id']);
        exit();
    }
}
header('Content-Disposition: attachment; filename='.$res[0]["name"]);
$passphrase = $res[0]["secret"];
$iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
$key = substr(md5("\x2D\xFC\xD8".$passphrase, true) .
md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
$opts = array('iv'=>$iv, 'key'=>$key);
$fp = fopen('./file/'.$res[0]["realname"].'.data', 'rb');
stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
fpassthru($fp);