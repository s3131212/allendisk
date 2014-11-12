<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
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
    foreach($query as $val){
        $result = $db->ExecuteSQL($val);
        if(!$result){
            throw new Exception($db->lastError);
        }
    }
}catch (Exception $e) {
    $error = true;
    $errormsg = base64_encode(json_encode(array(
        'type' => 'SQL Insert Error',
        'line' => __LINE__,
        'file' => dirname(__FILE__) . ';' . __FILE__,
        'errormsg' => $e->getMessage(),
    )));
}
if($error){
    echo "Oh no，SQL資訊好像有錯誤，請回上一頁確定資料是否有錯誤，如果確定無誤，請將以下內容複製下來並提交給Allen<br />".$errormsg;
}else{
    header("Location:site-setting.php");
}