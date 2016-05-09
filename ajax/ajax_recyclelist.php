<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include dirname(dirname(__FILE__)).'/config.php';
@set_time_limit(20);
if (!session_id()) {
    session_start();
}
function fileformat($type, $name){
    if (preg_match("/image\/(.*)/i", $type)) {
        return strtoupper(str_replace('image/', '', $type)).' 圖檔';
    } elseif (preg_match("/audio\/(.*)/i", $type)) {
        return strtoupper(str_replace('audio/', '', $type)).' 音樂檔';
    } elseif (preg_match("/video\/(.*)/i", $type)) {
        return strtoupper(str_replace('vedio/', '', $type)).' 影片檔';
    } elseif (preg_match("/text\/(.*)/i", $type)) {
        return '純文字檔';
    } elseif ($type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        return 'MS Office Word';
    } elseif ($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        return 'MS Office Excel';
    } elseif ($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
        return 'MS Office Powerpoint';
    } elseif ($type == 'application/x-bzip2' || $type == 'application/x-gzip' || $type == 'application/x-7z-compressed' || $type == 'application/x-rar-compressed' || $type == 'application/zip' || $type == 'application/x-apple-diskimage' || $type == 'application/x-tar') {
        return '壓縮檔';
    } else {
        return strtoupper(substr($name, -(strlen($name) - strrpos($name, '.') - 1))).'檔';
    }
}

$filelist = array(
    "file" => array(),
    "dir" => array()
);

$res = $db->select('file', array('owner' => $_SESSION['username'], 'recycle' => '1'));
if ($res[0]['id'] != null) {
    foreach ($res as $d) {
        if ($d['dir'] != 0) {
            $ordir = $db->select('dir', array('id' => $d['dir']));
            if ($ordir[0]['recycle'] == 1) {
                continue;
            }
            $ordir = $ordir[0]['name'];
        } else {
            $ordir = '主目錄';
        }
        array_push($filelist["file"], array(
            "name" => $d["name"],
            "id" => $d["id"],
            "fileformat" => fileformat($d['type'], $d['name']),
            "ordir" => $ordir
        ));
    }
}
$res = $db->select('dir', array('owner' => $_SESSION['username'], 'recycle' => '1'));
if ($res[0]['id'] != null) {
    foreach ($res as $d) {
        if ($d['parent'] != 0) {
            $ordir = $db->select('dir', array('id' => $d['parent']));
            if ($ordir[0]['recycle'] == 1) {
                continue;
            }
            $ordir = $ordir[0]['name'];
        } else {
            $ordir = '主目錄';
        }
        array_push($filelist["file"], array(
            "name" => $d["name"],
            "id" => $d["id"],
            "fileformat" => "資料夾",
            "ordir" => $ordir
        ));
    }
}

echo json_encode(array($filelist));
