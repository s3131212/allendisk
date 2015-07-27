<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
function scan_dir($location, $current) {
    $output = '';

    foreach ($GLOBALS['db']
        ->select('dir', [
            'parent'  => $location,
            'owner'   => $_SESSION["username"],
            'recycle' => '0'
        ]) as $d) {
        $sdir = $GLOBALS['db']->select('dir', [
            'parent'  => $d["id"],
            'owner'   => $_SESSION["username"],
            'recycle' => '0'
        ]);

        if ($current == $d["id"]) {
            $ct = ' class="current"';
        } else {
            $ct = '';
        }

        if ($sdir[0]["id"] != null) {
            $output .= '<li' . $ct . '><i class="fa fa-folder-open"></i> <a href="home.php?dir=' . $d["id"] . '" data-id="' . $d["id"] . '">' . $d["name"] . '</a>
                        <ul data-parent="' . $d["id"] . '">' . scan_dir($d["id"], $_GET["current"]) . '</ul></li>';
        } else {
            $output .= '<li' . $ct . '><i class="fa fa-folder"></i> <a href="home.php?dir=' . $d["id"] . '" data-id="' . $d["id"] . '" >' . $d["name"] . '</a></li>';
        }
    }

    return $output;
}

if ($_SESSION["login"]) {
    $output = scan_dir('0', $_GET["current"]);
    echo json_encode([
        [
            "content" => $output
        ]
    ]);
} else {
    echo json_encode([
        "content" => "您尚未登入"
    ]);
}
