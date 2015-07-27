<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
function delete_dir($id) {
    $result = true;

    foreach ($GLOBALS['db']
        ->select("file", [
            'owner' => $_SESSION["username"],
            'dir'   => $id
        ]) as $k) {
        $result = $GLOBALS['db']->update('file', [
            'recycle' => '1'
        ], [
            'id' => $k['id']
        ]);
    }

    $result = $GLOBALS['db']->update('dir', [
        'recycle' => '1'
    ], [
        'id' => $id
    ]);
    return $result;
}

function scan_dir($id) {
    $result = true;

    foreach ($GLOBALS['db']->select("dir", [
        'owner'  => $_SESSION["username"],
        'parent' => $id
    ]) as $d) {
        $result = scan_dir($d["id"]);
    }

    $result = delete_dir($id);
    return $result;
}

$res = $GLOBALS['db']->select('dir', [
    'id' => $_GET["id"]
]);

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) {
    $result = scan_dir($_GET['id']);
    echo json_encode([
        "success" => $result,
        "message" => $result ? "成功刪除。" : "刪除失敗。"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ]);
}
