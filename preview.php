<?php
include('config.php'); 
if(!session_id()) session_start();
function decode_file($id){
    $res=$GLOBALS['db']->select("file",array("id"=>$id));
    $passphrase = $res[0]["secret"];
    $iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
    $key = substr(md5("\x2D\xFC\xD8".$passphrase, true) .
    md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
    $opts = array('iv'=>$iv, 'key'=>$key);
    $fp = fopen('./file/'.$res[0]["realname"].'.data', 'rb');
    stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
    fpassthru($fp);
    return $fp;
}
if($_GET["id"]!=null){
    $res = $db->select("file",array('id' => $_GET["id"]));
    if($res[0]["owner"]!=$_SESSION["username"]){
        header("Location:index.php");
        exit();
    }
    if(preg_match("/image\/(.*)/i", $res[0]["type"])){
        echo '<img src="readfile.php?id='.$res[0]["id"].'" style="max-width:90%; max-height:90%; width:auto; height:auto;" />';
    }elseif(preg_match("/audio\/(.*)/i", $res[0]["type"])){
        echo '<script src="js/audio.min.js"></script>
        <script>
            audiojs.events.ready(function() {
                var as = audiojs.createAll();
            });
        </script>
        <audio src="readfile.php?id='.$res[0]["id"].'" preload="auto" style="width:100%;"/>';
    }elseif(preg_match("/video\/(.*)/i", $res[0]["type"])){
        echo '<video style="width:100%;height:90%;" controls><source src="readfile.php?id='.$res[0]["id"].'" type="'.$res[0]["type"].'">很抱歉，您的瀏覽器暫時無法預覽影片</video>';
    }elseif($res[0]["type"] == "text/html" || $res[0]["type"] == "text/plain"){
        echo decode_file($_GET["id"]);
    }
}