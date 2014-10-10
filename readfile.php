<?php include('config.php'); 	
$res=$db->select("file",array("id"=>$_GET['id']));
if($_GET['id']!=null){
$passphrase = $res[0]["secret"];
$iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
$key = substr(md5("\x2D\xFC\xD8".$passphrase, true) .
md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
$opts = array('iv'=>$iv, 'key'=>$key);
$fp = fopen('./file/'.$res[0]["realname"].'.data', 'rb');
stream_filter_append($fp, 'mdecrypt.rijndael-256', STREAM_FILTER_READ, $opts);
header('content-type: '.$res[0]["type"]);
fpassthru($fp);
}else{
  header("Location: index.php");
}