<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();

if ($_GET["type"] == "dir") {
    $res = $db->select("dir", [
        'id' => $_GET['id']
    ]);
} elseif ($_GET["type"] == "file") {
    $res = $db->select("file", [
        'id' => $_GET['id']
    ]);
}

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) {
    if ($_GET["type"] == "dir") {
        $result = $db->update('dir', [
            'color' => $_GET['color']
        ], [
            'id' => $_GET['id']
        ]);
    } elseif ($_GET["type"] == "file") {
        $result = $db->update('file', [
            'color' => $_GET['color']
        ], [
            'id' => $_GET['id']
        ]);
    }

    echo json_encode([
        "success" => $result,
        "message" => $result ? "成功標記檔案。" : "標記檔案失敗。"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是檔案的擁有者。"
    ]);
}
