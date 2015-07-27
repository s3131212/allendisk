<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();

$res = $db->select("file", [
    'id' => $_GET['id']
]);

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) {
    $result = $db->update('file', [
        'recycle' => '1'
    ], [
        'id' => $_GET['id']
    ]);

    echo json_encode([
        "success" => $result,
        "message" => $result ? "成功刪除檔案。" : "刪除檔案失敗。"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是檔案的擁有者。"
    ]);
}
