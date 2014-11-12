<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
function scan_dir($location){
    $output = '';
    if($location == $_SESSION["dir"]){
        $output .= '<li><a href="#" data-id="'.$d["id"].'" >' . $d["name"] . '</a></li>';
    }else{
        foreach ($GLOBALS['db']->select('dir',array('parent' => $location,'owner'=>$_SESSION["username"])) as $d) {
            $sdir = $GLOBALS['db']->select('dir',array('parent' => $d["id"],'owner'=>$_SESSION["username"]));
            if($sdir[0]["id"]!=null){
                $output .= '<li><span class="tree-indicator"><i class="ion-ios7-minus-empty"></i></span>
                            <a href="#" class="filetreeselector" data-id="'.$d["id"].'">' . $d["name"] . '</a>
                            <ul class="dir-tree">' . scan_dir($d["id"]) . '</ul></li>';
            }else{
                $output .= '<li><a href="#" class="filetreeselector" data-id="'.$d["id"].'" >' . $d["name"] . '</a></li>';
            }
        }
    }
    return $output;
}
if($_SESSION["login"]){
    $output = scan_dir('0');
    echo json_encode(array(array(
        "content" => $output
    )));
}else {
    echo json_encode(array(
        "content" => "您尚未登入"
    ));
}