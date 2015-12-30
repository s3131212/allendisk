<?php

/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
session_name($_COOKIE['session_name']);
session_start();
session_destroy();
header('Location: login.php');
