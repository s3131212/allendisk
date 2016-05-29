<?php
/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include 'config.php';
if (!session_id()) {
    session_start();
}
if (!$_SESSION['login']) {
    exit();
}

@ignore_user_abort(false);
@set_time_limit(0);

function sizecount($size){
    if ($size < 0.001) {
        return round(($size * 1000 * 1000), 2).'B';
    } elseif ($size >= 0.001 && $size < 1) {
        return round(($size * 1000), 2).'KB';
    } elseif ($size >= 1 && $size < 1000) {
        return round($size, 2).'MB';
    } elseif ($size >= 1000) {
        return round(($size / 1000), 2).'GB';
    }
}

//Code edited from https://github.com/23/resumable.js/blob/master/samples/Backend%20on%20PHP.md

function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$total_files) {

    // count all the parts of this file
    $total_files_on_server_size = 0;
    $temp_total = 0;
    foreach(scandir($temp_dir) as $file) {
        $temp_total = $total_files_on_server_size;
        $tempfilesize = filesize($temp_dir.'/'.$file);
        $total_files_on_server_size = $temp_total + $tempfilesize;
    }
    // check that all the parts are present
    // If the Size of all the chunks on the server is equal to the size of the file uploaded.
    if ($total_files_on_server_size >= $totalSize) {
    // create the final destination file 
        if (($fp = fopen('file/'.$fileName.'.temp', 'w')) !== false) {
            for ($i=1; $i<=$total_files; $i++) {
                if(!file_exists($temp_dir.'/'.$fileName.'.part'.$i)){
                    rrmdir($temp_dir);
                    return false;
                }
                fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
            }
            fclose($fp);
        } else {
            rrmdir($temp_dir);
            return false;
        }

        // rename the temporary directory (to avoid access from other 
        // concurrent chunks uploads) and than delete it
        return $total_files_on_server_size;
    }
    return false;
}
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object); 
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
function finish_upload($temp_dir, $resumableFilename, $resumableChunkSize, $resumableTotalSize, $resumableTotalChunks){
    global $config;
    global $db;
        if(createFileFromChunks($temp_dir, $_SESSION['file_name'][$resumableFilename],$resumableChunkSize, $resumableTotalSize,$resumableTotalChunks) !== false){

            $result = '';
            $filesize = filesize('file/'.$_SESSION['file_name'][$resumableFilename].'.temp');

            if ($config['size'] != 0) {
                if ($filesize > ($config['size'] * 1000 * 1000)) {
                    $result = 'sizeout';
                    unlink('file/'.$_SESSION['file_name'][$resumableFilename].'.temp');
                }
            }
            if ($config['total'] != 0) {
                $used = $db->ExecuteSQL(sprintf('SELECT SUM(`size`) AS `sum` FROM `file` WHERE `owner` = \'%s\' AND `recycle` = \'0\'', $db->SecureData($_SESSION['username'])));
                if ($used[0]['sum'] >= ($config['total'] * 1000 * 1000)) {
                    $result = 'totalout';
                    unlink('file/'.$_SESSION['file_name'][$resumableFilename].'.temp');
                }
            }

            if($result == ''){

                //Get File Info
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $filetype = @finfo_file($finfo, 'file/'.$_SESSION['file_name'][$resumableFilename].'.temp');
                finfo_close($finfo);

                $mkid = sha1(mt_rand().uniqid());
                $db->insert(array('name' => $resumableFilename, 'size' => $filesize, 'owner' => $_SESSION['username'], 'id' => $mkid, 'realname' => $_SESSION['file_name'][$resumableFilename], 'secret' => '', 'type' => $filetype, 'dir' => $_SESSION['dir'], 'recycle' => '0'), 'file');
                unset($_SESSION['file_name'][$resumableFilename]);
                $result = 'success';
            }

            $return = array(
                'result' => $result,
                'id' => $mkid,
                'name' => $resumableFilename,
                'size' => sizecount($filesize / 1000 / 1000)
            );
            echo json_encode($return);
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            $return = array(
                'result' => 'par',
                'id' => 'Unknown',
                'name' => 'Unknown',
                'size' => 'Unknown'
            );
            echo json_encode($return);
            exit();
        }
}

//Check Chunk 還有一些 Bug 需要修
if (isset($_GET['resumableFilename'])) {

    //Initialize
    if(isset($_GET['resumableFilename']) && isset($_GET['resumableChunkNumber'])){
        if($_GET['resumableChunkNumber'] == 1){
            $_SESSION['file_name'][$_GET['resumableFilename']] = sha1(md5(mt_rand().uniqid()));
        }
    }

    if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
        $_GET['resumableIdentifier']='';
    }

    $_GET['resumableIdentifier'] = preg_replace("/[^A-Za-z0-9]/",'',$_GET['resumableIdentifier']);
    $temp_dir = 'temp/'.$_GET['resumableIdentifier'];

    if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
        $_GET['resumableFilename']='';
    }
    if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
        $_GET['resumableChunkNumber']='';
    }
    $chunk_file = $temp_dir.'/'.$_SESSION['file_name'][$_GET['resumableFilename']].'.part'.$_GET['resumableChunkNumber'];
    if (file_exists($chunk_file)) {
        header("HTTP/1.0 200 Ok");
        if($_GET['resumableTotalChunks'] == $_GET['resumableChunkNumber']){
            finish_upload($temp_dir, $_GET['resumableFilename'], $_GET['resumableChunkSize'], $_GET['resumableTotalSize'], $_GET['resumableTotalChunks']);
        }
    } else {
        header("HTTP/1.0 404 Not Found");
    }
    exit();
}

// loop through files and move the chunks to a temporarily created directory
if (!empty($_FILES)) foreach ($_FILES as $file) {

    // check the error status
    if ($file['error'] != 0) {
        header("HTTP/1.1 500 Internal Server Error");
        continue;
    }

    if(!isset($_SESSION['file_name'][$_POST['resumableFilename']])){
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    // init the destination file (format <filename.ext>.part<#chunk>
    // the file is stored in a temporary directory
    if(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!=''){
        $_POST['resumableIdentifier'] = preg_replace("/[^A-Za-z0-9]/",'',$_POST['resumableIdentifier']);
        $temp_dir = 'temp/'.$_POST['resumableIdentifier'];
    }
    $dest_file = $temp_dir.'/'.$_SESSION['file_name'][$_POST['resumableFilename']].'.part'.$_POST['resumableChunkNumber'];

    // create the temporary directory
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0777, true);
    }

    // move the temporary file
    if (move_uploaded_file($file['tmp_name'], $dest_file)) {
        // check if all the parts present, and create the final destination file

        if($_POST['resumableTotalChunks'] == $_POST['resumableChunkNumber']){
            finish_upload($temp_dir, $_POST['resumableFilename'], $_POST['resumableChunkSize'], $_POST['resumableTotalSize'], $_POST['resumableTotalChunks']);
        }
    }
}