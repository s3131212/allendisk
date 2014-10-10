<?php
include('database.php'); 
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