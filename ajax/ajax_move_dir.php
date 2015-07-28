<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
require_once dirname(dirname(__FILE__) . '/require.php');
_session_start();
$res = $GLOBALS['db']->select('dir', [
    'id' => $_GET["id"]
]);

if ($_SESSION["login"] && $_SESSION["username"] == $res[0]["owner"] && isset($_GET['dir']) && isset($_GET['id'])) {
    $result = $GLOBALS['db']->update('dir', [
        'parent' => $_GET['dir']
    ], [
        'id' => $_GET['id']
    ]);
    echo json_encode([
        "success" => $result,
        "message" => $result ? "成功移動。" : "移動失敗。"
    ]);
    $token = fopen(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($_SESSION['username']) . '.token', "w");
    fclose($token);
} else {
    echo json_encode([
        "success" => false,
        "message" => "你不是資料夾的擁有者。"
    ]);
}
