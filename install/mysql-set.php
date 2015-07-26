<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
if(file_exists("install.lock")){
    header("Location: ../index.php");
    exit();
}
$error = false;
$errormsg = null;
try {
    if($_GET['update'] != 'true'){
        if(isset($_POST['dbname']) && $_POST['dbname'] != ''){
            $mysql_file = '../database.php';
            $mysql_sample_file = 'database-sample.php';
            $mysql_config = vsprintf(file_get_contents($mysql_sample_file), array(
                addslashes($_POST['host']),
                addslashes($_POST['dbname']),
                addslashes($_POST['username']),
                addslashes($_POST['password'])
            ));
            
            file_put_contents($mysql_file,$mysql_config);
        }

        require_once('../database.php');

        $query = file("install.sql");
    }else{
        // Update from 1.4
        require_once('../database.php');
        $query = file("update14.sql");
    }
    foreach($query as $val){
        $result = $db->ExecuteSQL($val);
        if(!$result){
            throw new Exception($db->lastError);
        }
    }
}catch (Exception $e) {
    $error = true;
    $errormsg = array(
        'type' => 'SQL Insert Error',
        'line' => __LINE__,
        'file' => dirname(__FILE__) . ';' . __FILE__,
        'errormsg' => $e->getMessage(),
    );
}
if($error){
    echo "SQL 發生錯誤：";
    print_r($errormsg);
    echo '若您無法自行解決，請嘗試聯絡 Allen Disk 開發者，或直接聯絡 Allen ( <a href="mailto:s3131212@gmail.com">s3131212@gmail.com</a> ) ，並將此頁訊息完整題交給我們，我們會儘速為您解答';
}else{
    if($_GET['update'] != 'true'){
        header("Location:site-setting.php");
    }else{
        $install_token = fopen("install.lock", "w");
        fclose($install_token);
        header("Location: index.php?fin=fin");
    }
}