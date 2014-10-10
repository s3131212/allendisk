<?php
if(!session_id()) session_start();
if(!$_SESSION['login']) exit();
include('config.php'); 
$re = 0;
if(isset($_POST['pass'])&&isset($_POST['pass2'])){
    if($_POST['pass'] != $_POST['pass2']){
    	$re = 1;
    }else{
        if($_POST['pass']!=null){
        	$db->update('user',array('pass' => md5($_POST['pass'])), array('name' => $_SESSION['username']));
            $re = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div class="repsd">
    <?php if($re == 1) echo '<div class="alert alert-warning" role="alert">兩次密碼不相同，請重新輸入</div>';
    elseif($re == 2) echo '<div class="alert alert-success" role="alert">變更完成</div>';
    ?>
        <form action="setpass.php" method="post" role="form">
            <div class="form-group">
                <input type="password" name="pass" id="pass" placeholder="密碼" class="form-control" required/>
            </div>
            <div class="form-group">
                <input type="password" name="pass2" id="pass2" placeholder="確認密碼" class="form-control" required/>
            </div>
            <input type="submit" value="送出" class="btn btn-info"/>
        </form>
    </div>
</body>
</html>
