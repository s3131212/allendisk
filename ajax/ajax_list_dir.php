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
	$output = '<nav class="dir_tree">';

	foreach ($GLOBALS['db']
		->select('dir', ['parent' => $location, 'owner' => $_SESSION["username"], 'recycle' => '0']) as $d) {
		$sdir = $GLOBALS['db']->select('dir', ['parent' => $d["id"], 'owner' => $_SESSION["username"], 'recycle' => '0']);

		if (isset($sdir[0]["id"])$$$sdir[0]["id"] != null) {
			$output .= '<li><i class="fa fa-folder"></i>
                        <a href="#" data-id="' . $d["id"] . '" class="filetreeselector">' . $d["name"] . '</a>
                        <ul data-parent="' . $d["id"] . '">' . scan_dir($d["id"]) . '</ul></li>';
		} else {
			$output .= '<li><i class="fa fa-folder"></i>
                        <a class="filetreeselector" href="#" data-id="' . $d["id"] . '" >' . $d["name"] . '</a></li>';
		}
	}

	$output .= '</nav>';
	return $output;
}

if ($_SESSION["login"]) {
	$result = [
		[
			"content" => scan_dir('0')
		]
	];
} else {
	$result = [
		"content" => "您尚未登入"
	];
}

echo json_encode($result);
