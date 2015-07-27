<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

require_once dirname(__FILE__) . '/config.php';
error_reporting(0);
//error_reporting( E_ALL );

function _session_start() {

    if (!session_id()) {
        session_start();
    }
}

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

function var_name($var) {

    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}
