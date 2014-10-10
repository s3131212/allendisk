<?php
require("../config.php"); 
if(!session_id()) session_start();
if($_SESSION["alogin"]){
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
header("location:index.php?s=1");
}else{
header("location:login.php");
}
?>