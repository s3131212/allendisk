<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
if (!session_id()) {
    session_start();
}

$data = $_SESSION;
session_write_close();

ob_clean();
//ini_set('error_reporting', E_ALL);
ini_set('zlib.output_compression', 'Off');
if (function_exists('apache_setenv')) {
    @apache_setenv('no-gzip', 1);
}

$res = $db->select('file', array('id' => $_GET['id'], 'recycle' => '0'));
if (isset($_GET['pretoken'])) {
    $pretoken = base64_decode($_GET['pretoken']);
    $pretoken = json_decode($pretoken, 1);
    $token = $pretoken[0];
} else {
    $token['id'] = '';
    $token['time'] = 0;
    $token['dir'] = '123';
}

if ($res[0]['owner'] == $data['username'] || $res[0]['share'] == '1' || ($token['id'] == $_GET['id'] && time() - $token['time'] < 15 && $token['dir'] == $res[0]['dir'])) {
    $size = intval($res[0]['size']);
    if (isset($_SERVER['HTTP_RANGE'])) {
        $ranges = array_map(
            'intval',
            explode(
                '-',
                substr($_SERVER['HTTP_RANGE'], 6)
            )
        );

        if (!$ranges[1]) {
            $ranges[1] = $size - 1;
        }
    }

    $passphrase = $_GET['password'];

    $iv = md5("\x1B\x3C\x58".$passphrase, true).md5("\x1B\x3C\x58".$passphrase, true);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase, true).md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
    $opts = array('iv' => $iv, 'key' => $key);

    $fp = fopen('./file/'.$res[0]['realname'].'.data', 'rb');
    stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);

    if (isset($ranges)) {
        header('HTTP/1.1 206 Partial Content');
        header('Accept-Ranges: bytes');
        header('Content-Length: '.($ranges[1] - $ranges[0]));
        header(
            sprintf(
                'Content-Range: bytes %d-%d/%d', // The header format
                $ranges[0], // The start range
                $ranges[1], // The end range
                $size // Total size of the file
            )
        );
    } else {
        header('HTTP/1.1 200 OK');
        header('Content-Length: '.$size);
    }

    header('Content-type: '.$res[0]['type']);

    if (isset($ranges)) {
        fseek($fp, $ranges[0]);

        while (ftell($fp) <= $ranges[1]) {
            if ($ranges[1] - ftell($fp) < 4096) {
                $chunk = $ranges - ftell($fp);
            } else {
                $chunk = 4096;
            }

            echo fread($fp, $chunk);

            flush();
            @ob_flush();
        }
    } else {
        $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, 'cbc');
        echo @substr(stream_get_contents($fp), 0, -($blocksize - ($res[0]['size'] % $blocksize)));
    }
} else {
    header('HTTP/1.1 403 Unauthorized');
    echo 'Permission Denied';
}
