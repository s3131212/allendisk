<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
@set_time_limit(20);

require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
function sizecount($size) {

    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2) . "B";
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2) . "KB";
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2) . "MB";
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2) . 'GB';
    }
}

function create_used_bar() {
    global $config;
    $audio = 0;
    $video = 0;
    $image = 0;
    $other = 0;
    $used = 0;

    foreach ($GLOBALS["db"]->select("file", [
        'owner' => $_SESSION["username"]
    ]) as $d) {
        $used += $d["size"];

        if (preg_match("/image\/(.*)/i", $d["type"])) {
            $image += $d["size"];
        } elseif (preg_match("/audio\/(.*)/i", $d["type"])) {
            $audio += $d["size"];
        } elseif (preg_match("/video\/(.*)/i", $d["type"])) {
            $video += $d["size"];
        } else {
            $other += $d["size"];
        }
    }

    if ($config["total"] != 0) {
        $output = '<div class="progress">';
        $used_list = '<ul class="list-unstyled">';

        if ($audio != 0) {
            $output .= '<div class="progress-bar progress-bar-success"  role="progressbar" style="width:' . round(($audio / 1000 / 1000 / $config["total"] * 100), 1) . '%;"></div>';
            $used_list .= '<li class="text-success">音樂： ' . sizecount(($audio / 1000 / 1000)) . ' ( ' . round(($audio / 1000 / 1000 / $config["total"] * 100), 1) . '% )</li>';
        } else {
            $used_list .= '<li class="text-success">音樂： 0MB ( 0% )</li>';
        }

        if ($video != 0) {
            $output .= '<div class="progress-bar progress-bar-info"  role="progressbar" style="width:' . round(($video / 1000 / 1000 / $config["total"] * 100), 1) . '%;"></div>';
            $used_list .= '<li class="text-info">影片： ' . sizecount(($video / 1000 / 1000)) . ' ( ' . round(($video / 1000 / 1000 / $config["total"] * 100), 1) . '% )</li>';
        } else {
            $used_list .= '<li class="text-info">影片： 0MB ( 0% )</li>';
        }

        if ($image != 0) {
            $output .= '<div class="progress-bar progress-bar-warning"  role="progressbar" style="width:' . round(($image / 1000 / 1000 / $config["total"] * 100), 1) . '%;"></div>';
            $used_list .= '<li class="text-warning">照片： ' . sizecount(($image / 1000 / 1000)) . ' ( ' . round(($image / 1000 / 1000 / $config["total"] * 100), 1) . '% )</li>';
        } else {
            $used_list .= '<li class="text-warning">照片： 0MB ( 0% )</li>';
        }

        if ($other != 0) {
            $output .= '<div class="progress-bar"  role="progressbar" style="width:' . round(($other / 1000 / 1000 / $config["total"] * 100), 1) . '%; background-color: #666666;"></div>';
            $used_list .= '<li>其他： ' . sizecount(($other / 1000 / 1000)) . ' ( ' . round(($other / 1000 / 1000 / $config["total"] * 100), 1) . '% )</li>';
        } else {
            $used_list .= '<li>其他： 0MB ( 0% )</li>';
        }

        $output .= "</div>";
        $output .= '<p>已經使用' . sizecount($config["total"]) . '中的' . sizecount(($used / 1000 / 1000)) . ' ( ' . round($used / 1000 / 1000 / $config["total"] * 100, 2) . '% )</p>';
        $used_list .= "</ul>";
    } else {
        $output = '<p><br/>已經使用' . sizecount(($used / 1000 / 1000)) . '</p>';
        $used_list = '<ul class="list-unstyled">';

        if ($audio != 0) {
            $used_list .= '<li class="text-success">音樂： ' . sizecount(($audio / 1000 / 1000)) . '</li>';
        } else {
            $used_list .= '<li class="text-success">音樂： 0MB</li>';
        }

        if ($video != 0) {
            $used_list .= '<li class="text-info">影片： ' . sizecount(($video / 1000 / 1000)) . '</li>';
        } else {
            $used_list .= '<li class="text-info">影片： 0MB</li>';
        }

        if ($image != 0) {
            $used_list .= '<li class="text-warning">照片： ' . sizecount(($image / 1000 / 1000)) . '</li>';
        } else {
            $used_list .= '<li class="text-warning">照片： 0MB</li>';
        }

        if ($other != 0) {
            $used_list .= '<li>其他： ' . sizecount(($other / 1000 / 1000)) . '</li>';
        } else {
            $used_list .= '<li>其他： 0MB</li>';
        }

        $output .= "</div>";
        $used_list .= "</ul>";
    }

    echo $output . $used_list;
}

create_used_bar();
exit;
