<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('database.php'); 
error_reporting(0);
$sitename=$db->select("setting",array("name"=>"sitename")); 
$config["sitename"]=$sitename[0]["value"];
$sitetitle=$db->select("setting",array("name"=>"sitetitle")); 
$config["sitetitle"]=$sitetitle[0]["value"];
$size=$db->select("setting",array("name"=>"size")); 
$config["size"]=$size[0]["value"];
$url=$db->select("setting",array("name"=>"url")); 
$config["url"]=$url[0]["value"];
$total=$db->select("setting",array("name"=>"total")); 
$config["total"]=$total[0]["value"];
$updatesec=$db->select("setting",array("name"=>"updatesec")); 
$config["updatesec"]=$updatesec[0]["value"];
$subtitle=$db->select("setting",array("name"=>"subtitle")); 
$config["subtitle"]=$subtitle[0]["value"];
$reg=$db->select("setting",array("name"=>"reg")); 
if ($reg[0]["value"]=="true") {
	$config["reg"]=true;
}else{
	$config["reg"]=false;
}
$why=$db->select("setting",array("name"=>"why")); 
if ($why[0]["value"]=="true") {
	$config["why"]=true;
}else{
	$config["why"]=false;
}
$tos=$db->select("setting",array("name"=>"tos")); 
if ($tos[0]["value"]=="true") {
	$config["tos"]=true;
}else{
	$config["tos"]=false;
}
?>
