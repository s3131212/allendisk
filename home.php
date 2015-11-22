<?php
/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
@set_time_limit(20);
include 'config.php';
if (!session_id()) {
    session_start();
}
if (!$_SESSION['login']) {
    header('Location:login.php');
    exit();
}
function sizecount($size)
{
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

function getparentdir($id)
{
    global $dirlocation;
    if ($id != '0') {
        $p = $GLOBALS['db']->select('dir', array('id' => $id, 'owner' => $_SESSION['username'], 'recycle' => '0'));
        $dirlocation[] = array('id' => $id, 'name' => $p[0]['name']);
        if ($p[0]['parent'] != '0') {
            getparentdir($p[0]['parent']);
        }
    }
}

$info = $db->select('user', array('name' => $_SESSION['username']));
if (isset($_GET['dir']) && $_GET['dir'] != null) {
    $_SESSION['dir'] = $_GET['dir'];
} else {
    header('location: home.php?dir=0');
}
if ($_GET['dir'] != '0') {
    $dir = $db->select('dir', array('id' => $_GET['dir'], 'owner' => $_SESSION['username']));
    if ($dir[0]['name'] == null) {
        header('location: home.php?dir=0');
        exit();
    }
}
$dirlocation = array();
getparentdir($_GET['dir']);
$dirlocation = array_reverse($dirlocation);
?>
<!DOCTYPE html>
<html>
<head onselect='return false;'>
	<title>
		<?php echo $config[ 'sitename'];?>
	</title>
	<meta charset="utf-8" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<script src="js/sweet-alert.min.js"></script>
    <link rel="stylesheet" href="css/sweet-alert.css">
	<script>
		var dir = '<?php echo $_SESSION['dir']; ?>';
	</script>
	<script src="js/modernizr.js" charset="utf-8"></script>
	<script src="js/script.js" charset="utf-8"></script>
	<style>
		html,
		body {
			height: 100%;
			/* 避免影響 jQuery selectable */
			
			-moz-user-select: none;
			-webkit-user-select: none;
		}
		
		#file_list_container {
			margin-top: 0;
			margin-bottom: 0;
			min-height: 100vh;
		}

		#contextMenu {
		  	position: absolute;
		  	display:none;
		  	z-index: 10000;
		}
		
		nav.dir_tree ul,
		nav.dir_tree li {
			margin: 0;
			padding: 0;
			list-style: none;
		}
		
		ul.list-home ul {
			border-left: 1px solid #DDD
		}
		
		nav.dir_tree ul li {
			margin-left: 15px
		}
		
		nav.dir_tree ul li a {
			color: #444
		}
		
		nav.dir_tree ul li.current>a,nav.dir_tree ul li.current>i {
			color: #2196f3
		}

		.tree_selected{
			background-color: #666666;
			color: #ffffff !important;
		}

		.filetreeselector:hover{
			cursor: pointer;
		}

		#color-tag .btn i {    			
			opacity: 0;				
		}
		#color-tag .btn.active i {				
			opacity: 1;				
		}
		.btn-black i {
			color:#FFF
		}

		.panel {
			border: 2px solid transparent;
			padding: 3px;
		}
		
		.panel-body {
			padding: 0;
			text-align: center;
			overflow: hidden;
		}
		
		.panel-body i {
			margin: 25px
		}
		
		.panel-body img {
			width: 100%
		}

		.panel-footer {
			height:3em;
			overflow:hidden;
		}
		
		.panel:hover,
		.panel:active {
			border-color: #2196f3;
		}

		.ui-selected {
			border-color: #1a78c2 !important;
		}

		.btn-black{
			background-color: #666666;
		}
		
		.tag-info {
			border-left: 4px solid #9C27B0 !important;
		}
		
		.tag-success {
			border-left: 4px solid #4CAF50 !important;
		}
		
		.tag-warning {
			border-left: 4px solid #FF9800 !important;
		}
		
		.tag-danger {
			border-left: 4px solid #E51C23 !important;
		}
		
		.tag-active {
			border-left: 4px solid #BBB !important;
		}

		.tag-black {
			border-left: 4px solid #666666 !important;
		}
		
		.tag-primary {
			border-left: 4px solid #2196F3 !important;
		}
		
		button.btn-arrow {
			padding: 5px !important
		}
		
		#usage_box {
			padding: 0
		}
		
		#usage_box p,#usage_box ul {
			padding: 0 10px;
		}
		
		.modal-dialog {
			z-index: 1050;
		}
		
		.dragover_box {
			background-color: #f8f8f8 !important;
		}

		.ui-selectable-helper{
            border: 1px solid #a9a9a9;
            background-color: rgba(169,169,169,0.3);
        }
	</style>
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid" style="max-width:1170px">
			<div class="col-md-2">
				<div class="navbar-header">
					<a class="navbar-brand" href="home.php?dir=0">
						<?php echo $config[ 'sitetitle']; ?>
					</a>
				</div>
			</div>
			<div class="col-md-8">
				<div class="collapse navbar-collapse">
					<div class="nav navbar-form navbar-left">
						<div class="btn-group btn-group-sm">
							<a href="home.php?dir=0" class="btn <?php echo ($_GET['dir'] == '0') ? 'btn-primary' : 'btn-default'; ?>"><i class="fa fa-home"></i> 首頁</a>
							<?php
                                if (!empty($dirlocation)) {
                                    foreach ($dirlocation as $key => $value) {
                                        ?>
										<button class="btn btn-default btn-arrow"><i class="fa fa-caret-right"></i></button>
										<a href="home.php?dir=<?php echo $value['id'];
                                        ?>" class="btn <?php echo ($value['id'] == $_GET['dir']) ? 'btn-primary' : 'btn-default';
                                        ?>"><i class="fa fa-folder"></i> <?php echo $value['name'];
                                        ?></a>
								<?php	
                                    }
                                }
                            ?>
						</div>
					</div>
					<div class="pull-left btn-group" id="action_btn_multi" style="display:none;">
					    <button class="btn btn-danger" type="button" id="delete-btn">刪除</button>
					    <button class="btn btn-default" type="button" id="move-btn">移動</button>
					    <button class="btn btn-primary" type="button" id="tag-btn">標記</button>
					</div>
				</div>
			</div>
			<div class="col-md-2">
				<ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
						<a href="#" id="user-info-btn" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $_SESSION[ 'username']?> <small><i class="fa fa-chevron-down"></i></small></a>
                        <ul class="dropdown-menu" id="user-info">
                            <li>
                                <div class="navbar-login" style="padding:5px 15px;width:200px;max-width:250px">
									<h5>帳號資訊</h5>
									<i class="fa fa-user fa-fw"></i> <?php echo $_SESSION['username']?><br/>
                                    <i class="fa fa-envelope fa-fw"></i> <?php echo $info[0]['email']?><br/>
                                    <i class="fa fa-history fa-fw"></i> <?php echo $info[0]['jointime']?>
                                </div>
                            </li>
							<li><a href="#" id="info-btn"><i class="fa fa-lock"></i> 更改密碼</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php"><i class="fa fa-power-off"></i> 登出</a></li>
                        </ul>
                    </li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container" style="margin:100px auto">
		<div class="col-md-3">
			<div class="well">
				<nav class="dir_tree">
					<div class="btn-group btn-group-justified">
						<a href="#" class="btn btn-primary" id="upload-btn"><i class="fa fa-upload"></i> 上傳檔案</a>
						<a href="#" class="btn btn-success" id="mkdir-btn"><i class="fa fa-plus"></i> 新資料夾</a>
					</div>
					<br/>
						<li<?php if ($_GET['dir'] == '0') {
    echo ' class="current"';
} ?>><i class="fa fa-home"></i> <a href="home.php?dir=0" data-id="0"  <?php if ($_GET['dir'] != '0') {
    echo ' style="color:#444;"';
} ?>>主目錄</a>
							<ul class="list-home" id="dir-tree">
							</ul>
						</li>
					<br/>
					<button class="btn btn-danger btn-block btn-sm" id="recycle-btn"><i class="fa fa-trash"></i> 垃圾桶</button>
				</nav>
			</div>
			<div class="well" id="usage_box"></div>
		</div>
		<div class="col-md-9">
			<div id="file_list_container"></div>
		</div>
	</div>
	<nav class="navbar navbar-default navbar-fixed-bottom">
		<div class="container-fluid text-center text-info">
			Proudly Powered by <a href="http://ad.allenchou.cc/">Allen Disk</a>
		</div>
	</nav>
	<div id="contextMenu" class="dropdown clearfix">
	    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:absolute;margin-bottom:5px;">
	    </ul>
	</div>
	<div id="upload-file" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>上傳檔案</h3>
				</div>
				<div class="modal-body">
					<p>檔案大小限制：
						<?php echo ($config[ 'size'] != 0) ? sizecount($config[ 'size']) : '無'; ?><?php if ($config['tos']) {
    ?>，當您上傳檔案，代表您已經同意<a href="tos.php" target="_blank">使用條款</a>了，如果您不同意，請勿上傳任何檔案<?php 
} ?></p>
					<p>如果您的瀏覽器較為老舊，或是要上傳較多檔案，請使用傳統上傳。 拖曳上傳如果卡在 100% ，代表正在進行加密程序，請耐心等候。</p>
					<p class="text-center"><a href="#" class="btn btn-info" id="ajax_upload_btn">拖曳上傳</a>&nbsp;<a href="#" class="btn btn-info" id="traditional_upload_btn">傳統上傳</a>&nbsp;<a href="#" class="btn btn-info" id="remote_upload_btn">遠端上傳</a>
					</p>
					<div id="upload_box" style="width:100%; height:200px; background-color:#eeeeee; -webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;">
						<div style="margin:0 auto; width:50%; font-size:25px; text-align:center; padding:50px;">
							<span>拖拉到此上傳</span>
							<br /><span style="font-size:15px;">或</span>
							<br />
							<div style="height:44px; width:94px; margin:0 auto;">
								<button class="btn primary" id="upload_button">瀏覽檔案</button>
								<input id="file" name="file[]" type="file" style="opacity:0 ;margin-top: -44px;width: 93px;height: 44px;" multiple>
								<br />
							</div>
						</div>
					</div>
					<div class="progress" id="upload_progress_box" style="margin-top:20px;">
						<div class="progress-bar progress-bar-striped active" id="upload_progress" style="width: 0%"></div>
					</div>
					<div id="upload_table_box">
						<table class="table">
							<tbody id="upload_table">
								<tr>
									<td>檔案名稱</td>
									<td>檔案大小</td>
									<td>上傳結果</td>
								</tr>
							</tbody>
						</table>
					</div>
					<iframe src="uploadiframe.php" style="border:none; width:100%; height:200px; display:none;" id="upload_iframe">您的瀏覽器暫時不支援 iframe </iframe>
					<iframe src="remotedownload.php" style="border:none; width:100%; height:200px; display:none;" id="remote_iframe">您的瀏覽器暫時不支援 iframe </iframe>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="preview-file" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>預覽檔案</h3>
				</div>
				<div class="modal-body">
					<iframe onload="javascript:resizeIframe(this);" src="preview.php" id="preview-iframe" style="border:none; width:100%; height:100%;">您的瀏覽器暫時不支援 iframe </iframe>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="info-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>更改密碼</h3>
				</div>
				<div class="modal-body">
					<iframe src="setpass.php" style="border:none; width:100%;">您的瀏覽器暫時不支援 iframe ，請使用 <a href="setpass.php">此連結</a> 修改</iframe>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="mkdir-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>新增資料夾</h3>
				</div>
				<div class="modal-body">
					<iframe src="mkdir.php" style="border:none; width:100%; height:200px;">您的瀏覽器暫時不支援 iframe ，請使用 <a href="mkdir.php">此連結</a> 新增</iframe>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="mvfile-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>移動檔案</h3>
				</div>
				<div class="modal-body">
					<nav class="dir_tree">
					<ul>
						<li><i class="fa fa-home"></i> <a data-id="0" class="filetreeselector">主目錄</a>
							<ul class="list-home" id="ajax_load_tree"></ul>
						</li>
					<ul>
					</nav>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" id="move-submit-btn">移動檔案</button>
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="recycle-box" class="modal fade">
		<div class="modal-lg modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>回收筒</h3>
				</div>
				<div class="modal-body">
					<button class="btn btn-default" id="real_delete_all">清空</button>
					<table class="table">
						<thead>
							<tr>
								<td>檔案名稱</td>
								<td style="max-width: 400px;">檔案類型</td>
								<td>原始位置</td>
								<td style="min-width: 200px;">動作</td>
							</tr>
						</thead>
						<tbody id="recycle_list">
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
	<div id="color-tag" class="modal fade">
		<div class="modal-sm modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>顏色標注</h3>
				</div>
				<div class="modal-body">
				<!--
					<button type="button" class="btn btn-lg btn-block color-tag-btn btn-default" id="">無色</button>
					<button type="button" class="btn btn btn-lg btn-block color-tag-btn" id="active">灰色</button>
					<button type="button" class="btn btn btn-lg btn-block color-tag-btn btn-primary" id="primary">藍色</button>
					<button type="button" class="btn btn-success btn-lg btn-block color-tag-btn" id="success">綠色</button>
					<button type="button" class="btn btn-info btn-lg btn-block color-tag-btn" id="info">紫色</button>
					<button type="button" class="btn btn-warning btn-lg btn-block color-tag-btn" id="warning">黃色</button>
					<button type="button" class="btn btn-danger btn-lg btn-block color-tag-btn" id="danger">紅色</button>
				-->
					<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">

						<label class="btn btn-default color-tag-btn" id=""><!--default 是白色所以 id 不用設定-->
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>
						<label class="btn color-tag-btn btn-black" id="black">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>
						<label class="btn btn-success color-tag-btn" id="success">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>
						<label class="btn btn-primary color-tag-btn" id="primary">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>			
						<label class="btn btn-info color-tag-btn" id="info">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>			
						<label class="btn btn-warning color-tag-btn" id="warning">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>			
						<label class="btn btn-danger color-tag-btn" id="danger">
							<input type="radio" autocomplete="off" name='color-tag'>
							<i class="fa fa-check"></i>
						</label>			
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">關閉</button>
				</div>
			</div>
		</div>
	</div>
</body>

</html>