<?php
/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
include('config.php'); 
if(!session_id()) session_start();
    foreach($db->select("file",array('owner' => $_SESSION["username"],'recycle'=>'1')) as $d){
        if($d["dir"]!=0){
            $ordir = $db->select('dir',array('id'=>$d["dir"]));
            if($ordir[0]["recycle"]==1) continue;
            $ordir = $ordir[0]["name"];
        }else{
            $ordir = "主目錄";
        }
    ?>
        <tr>
            <td><?php echo $d["name"]; ?></td>
            <td><?php echo $d["type"]; ?></td>
            <td><?php echo $ordir; ?></td>
            <td>
                <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="file" class="btn btn-default recycle_back">還原</a>
                <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="file" class="btn btn-danger real_delete">永久刪除</a>
            </td>
        </tr>
<?php }
    foreach($db->select("dir",array('owner' => $_SESSION["username"],'recycle'=>'1')) as $d){
        if($d["parent"]!=0){
            $ordir = $db->select('dir',array('id'=>$d["parent"]));
            if($ordir[0]["recycle"]==1) continue;
            $ordir = $ordir[0]["name"];
        }else{
            $ordir = "主目錄";
        }
    ?>
        <tr>
            <td><?php echo $d["name"]; ?></td>
            <td>資料夾</td>
            <td><?php echo $ordir; ?></td>
            <td>
                <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="dir" class="btn btn-default recycle_back">還原</a>
                <a href="#"  data-id="<?php echo $d["id"] ?>" data-type="dir" class="btn btn-danger real_delete">永久刪除</a>
            </td>
        </tr>
<?php }
?>