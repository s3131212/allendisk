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
if(isset($_GET["pretoken"])){
    $pretoken = base64_decode($_GET["pretoken"]);
    $pretoken = json_decode($pretoken,1);
    $token = $pretoken[0];
}else{
    $token["id"] = "";
    $token["time"] = 0;
    $token["dir"] = "123";
}
if($res[0]["owner"]==$_SESSION["username"] || $res[0]["share"]=="1" || ($token["id"]==$_GET["id"] && time()-$token["time"] < 15 && $token["dir"]==$res[0]['dir'])){
    $passphrase = $res[0]["secret"];
    $iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase, true) .
    md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
    $opts = array('iv'=>$iv, 'key'=>$key);
    $fp = fopen('./file/'.$res[0]["realname"].'.data', 'rb');
    stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
    header('Content-type: '.$res[0]["type"]);
    fpassthru($fp);
}else{
    echo 'Permission Denied';
}