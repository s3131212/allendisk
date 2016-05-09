<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

@set_time_limit(20);
include dirname(dirname(__FILE__)).'/config.php';
if (!session_id()) {
    session_start();
}

function sizecount($size)
{
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
function decodepassphrase($secret){
    if($secret != ''){
        /* Decode Password */
        $passphrase['b'] = $_SESSION['password'];
        $passphrase['c'] = $secret;
        $iv = md5("\x1B\x3C\x58".$passphrase['b'], true).md5("\x1B\x3C\x58".$passphrase['b'], true);
        $key = substr(md5("\x2D\xFC\xD8".$passphrase['b'], true).md5("\x2D\xFC\xD9".$passphrase['b'], true), 0, 24);
        $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
    }else{
        $passphrase['a'] = 'nopassword';
    }
    return $passphrase['a'];
}
function linkcheck($type, $id, $name, $secret){
    $passphrase['a'] = decodepassphrase($secret);
    if (preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type) || preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == 'application/pdf' || $type == 'application/x-shockwave-flash') {
        return true;
    } else {
        return false;
    }
}

function previewcheck($type, $id)
{
    if (preg_match("/image\/(.*)/i", $type) || preg_match("/audio\/(.*)/i", $type) || preg_match("/video\/(.*)/i", $type) || preg_match("/text\/(.*)/i", $type) || $type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        return true;
        echo '<a href="#" data-preview-id="'.$id.'" class="btn btn-warning">預覽</a>';
    } else {
        return false;
        echo '<a href="#" class="btn btn btn-warning disabled">預覽</a>';
    }
}
function sharedir($share, $id){
    return $share;
}

function fileicon($type, $name){
    if (preg_match("/image\/(.*)/i", $type)) {
        return 'fa-file-image-o';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        return 'fa-file-sound-o';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        return 'fa-file-movie-o';
    } elseif (preg_match("/text\/(.*)/i", $type)) {
        return 'fa-file-text-o';
    } elseif ($type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        return 'fa-file-word-o';
    } elseif ($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        return 'fa-file-excel-o';
    } elseif ($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
        return 'fa-file-powerpoint-o';
    } elseif ($type == 'application/x-bzip2' || $type == 'application/x-gzip' || $type == 'application/x-7z-compressed' || $type == 'application/x-rar-compressed' || $type == 'application/zip' || $type == 'application/x-apple-diskimage' || $type == 'application/x-tar') {
        return 'fa-file-zip-o';
    } else {
        return 'fa-file-o';
    }
}

$filelist = array(
    "file" => array(),
    "dir" => array()
);
$file = $db->select('file', array('owner' => $_SESSION['username'], 'dir' => $_SESSION['dir'], 'recycle' => '0'));
$dir = $db->select('dir', array('owner' => $_SESSION['username'], 'parent' => $_SESSION['dir'], 'recycle' => '0'));
if ($file[0]['id'] == null && $dir[0]['id'] == null) {
    echo json_encode(array($filelist));
    exit();
}
foreach($dir as $d){
    array_push($filelist['dir'], array(
        "id" => $d['id'],
        "name" => $d[ 'name'],
        "color" => ($d['color'] != null) ? 'tag-'.$d['color'] : '',
        "share" => sharedir($d['share'], $d['id'])
    ));
}
foreach($file as $d){
    array_push($filelist['file'], array(
        "id" => $d['id'],
        "name" => $d[ 'name'],
        "color" => ($d['color'] != null) ? 'tag-'.$d['color'] : '',
        "icon" => fileicon($d['type'], $d['name']),
        "passphrase" => decodepassphrase($d['secret']),
        "share" => $d['share'],
        "linkcheck" => linkcheck($d['type'], $d['id'], $d['name'], $d['secret']),
        "preview" => previewcheck($d['type'], $d['id'])
    ));
}
echo json_encode(array($filelist));
