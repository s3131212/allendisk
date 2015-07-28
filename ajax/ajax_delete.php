<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();

$id = (isset($_GET['id'])) ? $_GET['id'] : "";
$res = $db->select("file", ['id' => $id]);

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"]) {
    $db->delete('file', ['id' => $id]);

    $result = @unlink(dirname(dirname(__FILE__)) . '/file/' . $res[0]["realname"] . ".data");

    echo json_encode([
        "success" => $result,
        "message" => $result ? "刪除檔案成功" : "刪除檔案失敗"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是檔案的擁有者"
    ]);
}
