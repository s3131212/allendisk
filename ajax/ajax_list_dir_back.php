<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
function scan_dir($location) {
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

        if ($sdir[0]["id"] != null) {
            $output .= '<li><span class="tree-indicator"><i class="ion-ios7-minus-empty"></i></span>
                        <a href="#" class="filetreeselector" data-id="' . $d["id"] . '">' . $d["name"] . '</a>
                        <ul class="dir-tree" data-parent="' . $d["id"] . '">' . scan_dir($d["id"]) . '</ul></li>';
        } else {
            $output .= '<li><a href="#" class="filetreeselector" data-id="' . $d["id"] . '" >' . $d["name"] . '</a></li>';
        }
    }

    return $output;
}

if ($_SESSION["login"]) {
    $output = scan_dir('0');
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
