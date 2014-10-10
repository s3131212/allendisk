<?php include('config.php'); 
if(!session_id()) session_start();
if(isset($_POST["name"])&&isset($_POST["password2"])&&isset($_POST["password"])&&$config["reg"]){
    $username = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $namecheck=$db->ExecuteSQL(sprintf("SELECT count(*) AS `count`  FROM `user` WHERE `name` = '%s'",mysql_real_escape_string($username,$db->databaseLink)));
    if($namecheck[0]["count"] > 0){
        $err = 2;
    }elseif ($username=="") {
        $err = 0;
    }elseif ($email=="") {
        $err = 0;
    }elseif ($password=="") {
        $err = 0;
    }elseif ($password!=$password2) {
        $err = 1;
    }else{
        $db->insert(array("name"=>$username,"pass"=>md5($password),"email"=>$email),"user");
        $err = 3;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>註冊 - <?php echo $config["sitename"];?></title>
        <meta charset="utf-8" />
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>body{ background-color: #F8F8F8; }</style>
        <script src="js/bootstrap.min.js"></script>
    </head>
    <body>
    <div class="container">
        <h1 class="text-center"><?php echo $config["sitetitle"]; ?></h1>
        <ul class="nav nav-tabs">
            <li><a href="index.php">首頁</a></li>
            <?php if($config["why"]){ ?><li><a href="why.php"><?php echo $config["sitename"];?>的好處</a></li><?php } ?>
            <li><a href="login.php">登入</a></li>
            <li class="active"><a href="#">註冊</a></li>
            <?php if($config["tos"]){ ?><li><a href="tos.php">使用條款</a></li><?php } ?>
        </ul>
        <?php if($config["reg"]){ ?>
        <?php 
        if($err=="1"){
          echo '<div class="alert alert-danger">兩次輸入的密碼必須相同</div>';
        }
        if($err=="0"){
          echo '<div class="alert alert-danger">不能有任何欄位是空白的</div>';
        }
        if($err=="2"){
          echo '<div class="alert alert-danger">已經有重複的帳號</div>';
        }if($err=="3"){
          echo '<div class="alert alert-success">註冊完成</div>';
        }
        ?>
        <form class="form-horizontal" action="reg.php" method="post" role="form">
            <div class="form-group">
                <label class="control-label" for="name">帳號</label>
                <div class="controls">
                    <input type="text" id="name" class="form-control" placeholder="帳號" name="name">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="email">Email</label>
                <div class="controls">
                    <input type="text" id="email" placeholder="Email" name="email" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="password">密碼</label>
                <div class="controls">
                    <input type="password" id="password" placeholder="Password" name="password" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="password2">重新輸入密碼</label>
                <div class="controls">
                <input type="password" id="password2" placeholder="Password" name="password2" class="form-control">
                </div>
            </div>
            <p>註冊後代表您已經同意<a href="tos.php">使用條款</a>且如果有違反，<?php echo $config["sitename"];?>將不必負任何責任</p>
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn">註冊</button>
                </div>
            </div>
        </form>
        <?php }else echo "<div class=\"alert alert-warning\" role=\"alert\">很抱歉，".$config["sitename"]."已經關閉註冊</div>"; ?>
        <p class="text-center text-info">Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a></p>
    </div>
    </body>
</html>