<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */
@set_time_limit(0);
require_once dirname(dirname(__FILE__)) . '/require.php';
_session_start();
$data = $_SESSION['username'];
$time = time();
session_write_close();

while (true) {
    clearstatcache();

    if ((time() - $time) >= 20) {
        break;
    }

    if (file_exists(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($data) . '.token')) {
        unlink(dirname(dirname(__FILE__)) . '/updatetoken/' . md5($data) . '.token');
        echo 'updatenow';
        @ob_flush();
        flush();
        break;
    } else {
        sleep(1);
    }
}
