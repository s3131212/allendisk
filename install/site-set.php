<?php
/**
 * Allen Disk 1.5
 * Copyright (C) 2012~2015 Allen Chou
 * Author: Allen Chou ( http://allenchou.cc )
 * License: MIT License
 */

if (file_exists("install.lock")) {
    header("Location: ../index.php");
    exit;
}

require "../config.php";

function var_name($var) {

    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}

$db->update('setting', ['value' => $_POST["sitename"]], ['name' => "sitename"]);
$db
    ->ExecuteSQL(sprintf("UPDATE `setting` SET `value` = '%s' WHERE `setting`.`name` = 'sitetitle';", $db
            ->databaseLink
            ->real_escape_string($_POST["sitetitle"])));

$settingsToUpdate = ["size", "url", "total", "admin", "subtitle"];

foreach ($settingsToUpdate as $key => $value) {
    $db->update('setting', ['value' => $_POST[$value]], ['name' => $value]);
}

$settingsToUpdate = ["tos", "why", "reg"];

foreach ($settingsToUpdate as $key => $value) {
    $$value = (!isset($_POST[$value]) || $_POST[$value] != "true") ? "false" : "true";
}

foreach ($settingsToUpdate as $key => $value) {
    $db->update('setting', ['value' => $value], ['name' => var_name($value)]);
}

header("Location: newuser-setting.php");
exit;
