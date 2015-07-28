<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
include 'config.php';

if (!session_id()) {
    session_start();
}

$res = $db->select("file", [
    "id"      => $_GET['id'],
    "recycle" => "0"
]);

if (!$_SESSION["login"] || $_SESSION["username"] != $res[0]["owner"]) {
    if ($_SERVER["HTTP_REFERER"] != $config["url"] . "downfile.php?id=" . $_GET['id'] . "&password=" . $_GET['password']) {
        header("Location: " . $config["url"] . "downfile.php?id=" . $_GET['id'] . "&password=" . $_GET['password']);
        exit;
    }

    if ($_GET['download'] == 'true') {
        exit;
    }
}

/* Decode Phrase */

if ($_GET['download'] == 'true') {
    $passphrase['b'] = $_SESSION['password'];
    $passphrase['c'] = $res[0]['secret'];
    $iv = substr(md5("\x1B\x3C\x58" . $passphrase['b'], true), 0, 8);
    $key = substr(md5("\x2D\xFC\xD8" . $passphrase['b'], true) . md5("\x2D\xFC\xD9" . $passphrase['b'], true), 0, 24);
    $passphrase['a'] = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($passphrase['c']), MCRYPT_MODE_CBC, $iv), "\0\3");
} else {
    $passphrase['a'] = $_GET['password'];
}

$iv = substr(md5("\x1B\x3C\x58" . $passphrase['a'], true), 0, 8);
$key = substr(md5("\x2D\xFC\xD8" . $passphrase['a'], true) . md5("\x2D\xFC\xD9" . $passphrase['a'], true), 0, 24);
$opts = [
    'iv'  => $iv,
    'key' => $key
];
$fp = fopen('./file/' . $res[0]["realname"] . '.data', 'rb');
stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: binary");
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="' . $res[0]["name"] . '"');
ob_end_flush();
fpassthru($fp);
