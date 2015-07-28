<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
include 'database.php';
error_reporting(0);

//error_reporting(E_ALL);

$sitename = $db->select("setting", [
    "name" => "sitename"
]);
$config["sitename"] = $sitename[0]["value"];

$sitetitle = $db->select("setting", [
    "name" => "sitetitle"
]);
$config["sitetitle"] = $sitetitle[0]["value"];

$size = $db->select("setting", [
    "name" => "size"
]);
$config["size"] = $size[0]["value"];

$url = $db->select("setting", [
    "name" => "url"
]);
$config["url"] = $url[0]["value"];

$total = $db->select("setting", [
    "name" => "total"
]);
$config["total"] = $total[0]["value"];

$subtitle = $db->select("setting", [
    "name" => "subtitle"
]);
$config["subtitle"] = $subtitle[0]["value"];

$reg = $db->select("setting", [
    "name" => "reg"
]);

if ($reg[0]["value"] == "true") {
    $config["reg"] = true;
} else {
    $config["reg"] = false;
}

$why = $db->select("setting", [
    "name" => "why"
]);

if ($why[0]["value"] == "true") {
    $config["why"] = true;
} else {
    $config["why"] = false;
}

$tos = $db->select("setting", [
    "name" => "tos"
]);

if ($tos[0]["value"] == "true") {
    $config["tos"] = true;
} else {
    $config["tos"] = false;
}
