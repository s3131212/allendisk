<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
$res = $db->select("dir", [
    'id' => $_GET['id']
]);

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) {
    $result = $db->update('dir', [
        'name' => $_GET["name"]
    ], [
        'id' => $_GET['id']
    ]);
    echo json_encode([
        "success" => $result,
        "message" => $result ? "成功重新命名資料夾。" : "重新命名失敗。"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ]);
}
