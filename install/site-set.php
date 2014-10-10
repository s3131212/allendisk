<?php
if(file_exists("install.lock")){
    header("Location: ../index.php");
    exit();
}
require("../config.php"); 
$db->update('setting',array('value' => $_POST["sitename"]), array('name' => "sitename"));
$db->update('setting',array('value' => $_POST["sitetitle"]), array('name' => "sitetitle"));
$db->update('setting',array('value' => $_POST["size"]), array('name' => "size"));
$db->update('setting',array('value' => $_POST["url"]), array('name' => "url"));
$db->update('setting',array('value' => $_POST["total"]), array('name' => "total"));
$db->update('setting',array('value' => $_POST["admin"]), array('name' => "admin"));

if ($_POST["tos"]!="true") {
    $tos="false";
}else{
    $tos="true";
}
$db->update('setting',array('value' => $tos), array('name' => "tos"));
if ($_POST["why"]!="true") {
    $why="false";
}else{
    $why="true";
}
$db->update('setting',array('value' => $why), array('name' => "why"));
if ($_POST["reg"]!="true") {
    $reg="false";
}else{
    $reg="true";
}
$db->update('setting',array('value' => $reg), array('name' => "reg"));
header("location:newuser-setting.php");