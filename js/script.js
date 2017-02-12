/*
Allen Disk 1.5
Copyright (C) 2012~2015 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/

var update = true;
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = 'expires='+ d.toUTCString();
    document.cookie = cname + '=' + cvalue + '; ' + expires;
}
function getCookie(cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return '';
}
function resizeIframe(obj) {
	var height = obj.contentWindow.document.body.scrollHeight;
	if (height === 'undefined' || height / $(window).height() < 0.5) {
		height = $(window).height() * 0.7;
	}
    obj.style.height = height + 'px';
}
$(function() {
	sweetAlertInitialize();
	function progressload(e) {
		if (e.lengthComputable) {
			$('#upload_progress').css('width', (e.loaded / e.total) * 100 + '%');
			$('#uploadpercentage').text(Math.round( (e.loaded / e.total) * 100 ) + '%');
		}
	}
	
	function filelist(json) {
		var filelist = { 'file': [], 'dir': [] };
		$.each(json[0]['dir'], function(key, val) {
		  	var html = "";
		  	html += '<div class="col-lg-3 col-md-4 col-sm-6">';
		  		html += '<div class="panel" data-id="'+val['id']+'" data-type="dir" data-download-url="home.php?dir='+val['id']+'">';
		  			html += '<div>';
		  				html += '<div class="panel-body"><i class="fa fa-4x fa-fw fa-folder"></i></div>';
		  				html += '<div class="panel-footer '+val['color']+'">';
		  					html += '<span>'+val['name']+'</span>';
		  				html += '</div>';
		  				html += '<div class="file-action" style="display: none;">';
		  					html += '<a href="home.php?dir='+val['id']+'" data-action="open"  class="btn btn-default">開啟</a>';
		  					html += '<a href="ajax_recycle_dir.php" data-action="delete" data-dir-delete-id="'+val['id']+'" class="">刪除</a>'
                			html += '<a href="ajax_rename_dir.php" data-action="rename" data-dir-rename-id="'+val['id']+'" class="">重新命名</a>';
                			html += '<a href="ajax_move_dir.php" data-action="move" data-dir-move-id="'+val['id']+'" class="">移動</a>';
                			if(val['share'] == 1){
                				html += '<a href="share_dir.php?id='+val['id']+'" data-action="share" target="_blank" class="">分享</a>';
        						html += '<a href="#" class="" data-share-id="'+val['id']+'" data-share-type="dir">取消公開資料夾</a>';
                			}else{
                				html += '<a href="#" data-action="share" class="disabled">分享</a>';
        						html += '<a href="#" class="" data-share-id="'+val['id']+'" data-share-type="dir" >公開資料夾</a>';
                			}
                			html += '<a href="#" data-action="tag" data-dir-color-id="'+val['id']+'" class="">標記</a>';
                		html += '</div>';
                	html += '</div>';
                html += '</div>';
            html += '</div>';
            filelist.dir.push(html);
		});
		$.each(json[0]['file'], function(key, val) {
		  	var html = "";
		  	html += '<div class="col-lg-3 col-md-4 col-sm-6">';
		  		html += '<div class="panel" data-id="'+val['id']+'" data-type="file" data-download-url="downfile.php?id='+val['id']+'&download=true">';
		  			html += '<div>';
		  				html += '<div class="panel-body"><i class="fa fa-4x fa-fw '+val['icon']+'"></i></div>';
		  				html += '<div class="panel-footer '+val['color']+'">';
		  					html += '<span>'+val['name']+'</span>';
		  				html += '</div>';
		  				html += '<div class="file-action" style="display: none;">';
		  					if(val['share'] == 1){
		  						html += '<a data-action="share" href="downfile.php?id='+val['id']+'&password='+val['passphrase']+'" target="_blank" class="">分享</a>';
		  						html += '<a href="#" class="" data-share-id="'+val['id']+'" data-share-type="file">取消公開檔案</a>';
		  					}else{
		  						html += '<a href="#" data-action="share" class="disabled">分享</a>';
		  						html += '<a href="#" class="" data-share-id="'+val['id']+'" data-share-type="file">公開檔案</a>';
		  					}
		  					if(val['linkcheck']){
		  						html += '<a data-action="link" href="readfile.php/'+val['name']+'?id='+val['id']+'&password='+val['passphrase']+'" target="_blank" class="">外連檔案</a>';
		  					}else{
		  						html += '<a href="#" class="disabled" data-action="link">外連檔案</a>';
		  					}
		  					html += '<a href="home.php?dir='+val['id']+'" data-action="open" class="">開啟</a>';
		  					html += '<a href="ajax_recycle.php" data-action="delete" data-delete-id="'+val['id']+'" class="">刪除</a>'
                			html += '<a href="ajax_rename.php" data-action="rename" data-rename-id="'+val['id']+'" class="">重新命名</a>';
                			html += '<a href="ajax_move.php" data-action="move" data-move-id="'+val['id']+'" class="">移動</a>';
                			if(val['preview'] == 1){
                				html += '<a href="#" data-action="preview" data-preview-id="'+val['id']+'" class="btn btn-warning">預覽</a>';
                			}else{
                				html += '<a href="#" data-action="preview" class="btn btn btn-warning disabled">預覽</a>';
                			}
                			html += '<a href="#" data-action="tag" data-color-id="'+val['id']+'" class="">標記</a>';
                		html += '</div>';
                	html += '</div>';
                html += '</div>';
            html += '</div>';
            filelist.file.push(html);
		});
		$('#dir_container').empty();
		$('#file_container').empty();
		$.each(filelist['dir'], function(key, val) {
			$('#dir_container').append(val);
		});
		$.each(filelist['file'], function(key, val) {
			$('#file_container').append(val);
		});
	};
	function recyclelist(json) {
		var filelist = { 'file': [], 'dir': [] };
		$.each(json[0]['file'], function(key, val) {
			var html = "";
			html += "<tr>";
			    html += "<td>"+val['name']+"</td>";
			    html += "<td>"+val['fileformat']+"</td>";
			    html += "<td>"+val['ordir']+"</td>";
			    html += "<td>";
			        html += '<div class="btn-group">';
			            html += '<a href="#"  data-id="'+val['id']+'" data-type="file" class="btn btn-default recycle_back">還原</a>';
			            html += '<a href="#"  data-id="'+val['id']+'" data-type="file" class="btn btn-danger real_delete">永久刪除</a>';
			        html += "</div>";
			    html += "</td>";
			html += "</tr>";
			filelist.file.push(html);
		});
		$.each(json[0]['dir'], function(key, val) {
			var html = "";
			html += "<tr>";
			    html += "<td>"+val['name']+"</td>";
			    html += "<td>資料夾</td>";
			    html += "<td>"+val['ordir']+"</td>";
			    html += "<td>";
			        html += '<div class="btn-group">';
			            html += '<a href="#"  data-id="'+val['id']+'" data-type="dir" class="btn btn-default recycle_back">還原</a>';
			            html += '<a href="#"  data-id="'+val['id']+'" data-type="dir" class="btn btn-danger real_delete">永久刪除</a>';
			        html += "</div>";
			    html += "</td>";
			html += "</tr>";
			filelist.dir.push(html);
		});
		$.each(filelist['dir'], function(key, val) {
			$('#recycle_list').append(val);
		});
		$.each(filelist['file'], function(key, val) {
			$('#recycle_list').append(val);
		});
	}
	function updateList() {
		$.ajax({
			url: 'ajax/ajax_filelist.php',
			type: 'GET',
			dataType: 'json',
			cache: false,
			timeout: 30000,
			data: {
				dir: dir
			},
			success: function(data) {
				filelist(data);
			}
		});
	};

	function updateRecycleList() {
		$.ajax({
			url: 'ajax/ajax_recyclelist.php',
			type: 'GET',
			dataType: 'json',
			cache: false,
			timeout: 30000,
			success: function(data) {
				$('#recycle_list').empty();
				recyclelist(data);
			}
		});
	};

	function updateUsage() {
		$.ajax({
			url: 'ajax/ajax_list_usage.php',
			type: 'GET',
			dataType: 'html',
			cache: false,
			timeout: 30000,
			success: function(data) {
				$('#usage_box').html(data);
			}
		});
	};
	function updateDirTree() {
		$.ajax({
			url: 'ajax/ajax_dir_tree.php',
			type: 'GET',
			dataType: 'json',
			data: {current: dir},
			success: function(data) {
				$('#dir-tree').html(data[0].content);
			},
		});
	};
	function updateAll() {
		updateList();
		updateUsage();
		updateRecycleList();
		updateDirTree();
	};
	function checkUpdate() {
		$.ajax({
			url: 'ajax/ajax_polling.php',
			type: 'GET',
			dataType: 'text',
			cache: false,
			timeout: 30000,
			success: function(data) {
				if (data == 'updatenow') {
					updateAll();
				}
				checkUpdate();
			},
			error: function() {
				checkUpdate();
			}
		});
	};


	$('body').on('click', '.tree-indicator', function() {
		var $state = $(this).html();
		$(this).parent().find('ul.dir-tree').slideToggle(500);
		$(this).html($state == '<i class="ion-ios7-plus-empty"></i>' ? '<i class="ion-ios7-minus-empty"></i>' : '<i class="ion-ios7-plus-empty"></i>');
	});

	$('#mkdir-modal').modal({
		show: false
	});

	$('#preview-file').modal({
		show: false
	});

	$('#info-modal').modal({
		show: false
	});

	$('#color-tag').modal({
		show: false
	});

	$('#upload-file').modal({
		show: false
	});

	$('#recycle-box').modal({
		show: false
	});

	/* Context Menu */
	var $contextMenu = $('#contextMenu');
	var $ContextMenuTarget = $('body');
	var ContextMenuType = '';
	$('body').on('click', ':not(#contextMenu)', function() {
		$contextMenu.hide();
	});
	$('body').on('contextmenu', '#file_list_container .panel[data-type=file] .panel-footer, #file_list_container .panel[data-type=file] .panel-body', function(e) {
		$contextMenu.hide();
		if ($('.ui-selected').length > 1) {
			ContextMenuType = 'multi';
			$contextMenu.find('ul').html('<li><a tabindex="-1" href="#" data-action="delete"><i class="fa fa-trash-o fa-fw"></i> 刪除</a></li><li><a tabindex="-1" href="#" data-action="move"><i class="fa fa-arrows fa-fw"></i> 移動</a></li><li><a tabindex="-1" href="#" data-action="tag"><i class="fa fa-tag fa-fw"></i> 標記</a></li>');
		}else {
			$contextMenu.find('ul').html('<li><a tabindex="-1" href="#" data-action="download"><i class="fa fa-download fa-fw"></i> 下載</a></li><li><a tabindex="-1" href="#" data-action="share"><i class="fa fa-share-square-o fa-fw"></i> 分享</a></li><li><a tabindex="-1" href="#" data-action="public"><i class="fa fa-cloud-upload fa-fw"></i> 公開/取消公開 檔案</a></li><li><a tabindex="-1" href="#" data-action="link"><i class="fa fa-globe fa-fw"></i> 外連檔案</a></li><li><a tabindex="-1" href="#" data-action="delete"><i class="fa fa-trash-o fa-fw"></i> 刪除</a></li><li><a tabindex="-1" href="#" data-action="rename"><i class="fa fa-edit fa-fw"></i> 重新命名</a></li><li><a tabindex="-1" href="#" data-action="move"><i class="fa fa-arrows fa-fw"></i> 移動</a></li><li><a tabindex="-1" href="#" data-action="preview"><i class="fa fa-eye fa-fw"></i> 預覽</a></li><li><a tabindex="-1" href="#" data-action="tag"><i class="fa fa-tag fa-fw"></i> 標記</a></li>');
		   	$ContextMenuTarget = $(e.target).parents('.panel');
		   	ContextMenuType = 'file';
		   	$('.ui-selected').removeClass('ui-selected');
		   	$ContextMenuTarget.addClass('ui-selected');
		   	//check preview
		   	if ($ContextMenuTarget.find('.file-action').find('[data-action=preview]').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=preview]').parent().remove();
		   	}

		   	//check link
		   	if ($ContextMenuTarget.find('.file-action').find('[data-action=link]').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=link]').parent().remove();
		   	}

		   	//check share
		   	if ($ContextMenuTarget.find('.file-action').find('[data-action=share]').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=share]').parent().remove();
		   	}

		   	//check public
		   	$('#contextMenu').find('a[data-action=public]').html('<i class="fa fa-cloud-upload fa-fw"></i> ' + $ContextMenuTarget.find('.file-action').find(':nth-child(2)').text());
	   	}
	   	$contextMenu.css({ display: 'block' });
		$contextMenu.position({
			my: 'left top',
			at: 'left bottom',
			of: e,
			collision: 'fit'
		});
	   	return false;
	});
	$('body').on('contextmenu', '#file_list_container .panel[data-type=dir] .panel-body, #file_list_container .panel[data-type=dir] .panel-footer', function(e) {
		$contextMenu.hide();
		if ($('.ui-selected').length > 1) {
			ContextMenuType = 'multi';
			$contextMenu.find('ul').html('<li><a tabindex="-1" href="#" data-action="delete"><i class="fa fa-trash-o fa-fw"></i> 刪除</a></li><li><a tabindex="-1" href="#" data-action="move"><i class="fa fa-arrows fa-fw"></i> 移動</a></li><li><a tabindex="-1" href="#" data-action="tag"><i class="fa fa-tag fa-fw"></i> 標記</a></li>');
		}else {
			$contextMenu.find('ul').html('<li><a tabindex="-1" href="#" data-action="open"><i class="fa fa-folder-open fa-fw"></i> 開啟</a></li><li><a tabindex="-1" href="#" data-action="delete"><i class="fa fa-trash-o fa-fw"></i> 刪除</a></li><li><a tabindex="-1" href="#" data-action="rename"><i class="fa fa-edit fa-fw"></i> 重新命名</a></li><li><a tabindex="-1" href="#" data-action="move"><i class="fa fa-arrows fa-fw"></i> 移動</a></li><li><a tabindex="-1" href="#" data-action="share"><i class="fa fa-share-square-o fa-fw"></i> 分享</a></li><li><a tabindex="-1" href="#" data-action="public"><i class="fa fa-globe fa-fw"></i> 公開/取消公開 資料夾</a></li><li><a tabindex="-1" href="#" data-action="tag"><i class="fa fa-tag fa-fw"></i> 標記</a></li>');
		   	$ContextMenuTarget = $(e.target).parents('.panel');
		   	ContextMenuType = 'dir';
		   	$('.ui-selected').removeClass('ui-selected');
		   	$ContextMenuTarget.addClass('ui-selected');

		   	//check share
		   	if ($ContextMenuTarget.find('.file-action').find('[data-action=share]').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=share]').parent().remove();
		   	}

		   	//check public
		   	$('#contextMenu').find('a[data-action=public]').html('<i class="fa fa-globe fa-fw fa-fw"></i> ' + $ContextMenuTarget.find('.file-action').find(':nth-child(6)').text());

		}
		$contextMenu.css({ display: 'block' });
		$contextMenu.position({
			my: 'left top',
			at: 'left bottom',
			of: e,
			collision: 'fit'
		});
	   	return false;
	});
	$contextMenu.on('click', 'a', function(e) {
		e.preventDefault();
	   	$contextMenu.hide();
	   	var action = $(this).attr('data-action');
	   	if (ContextMenuType == 'file') {
		   	switch (action) {
		   		case 'download':
					window.open($ContextMenuTarget.attr('data-download-url') , '_self');
		   			break;
		   		case 'share':
		   			window.open($ContextMenuTarget.find('.file-action').find('[data-action=share]').attr('href') , '_blank');
		   			break;
		   		case 'public':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(2)').click();
		   			break;
		   		case 'link':
		   			window.open($ContextMenuTarget.find('.file-action').find('[data-action=link]').attr('href') , '_blank');
		   			break;
		   		case 'delete':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=delete]').click();
		   			break;
		   		case 'rename':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=rename]').click();
		   			break;
		   		case 'move':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=move]').click();
		   			break;
		   		case 'preview':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=preview]').click();
		   			break;
		   		case 'tag':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=tag]').click();
		   			break;
		   	}
	   	}else if (ContextMenuType == 'dir') {
	   		switch (action) {
		   		case 'open':
					window.open($ContextMenuTarget.find('[data-action=open]').attr('data-download-url') , '_self');
		   			break;
		   		case 'delete':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=delete]').click();
		   			break;
		   		case 'rename':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=rename]').click();
		   			break;
		   		case 'move':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=move]').click();
		   			break;
		   		case 'share':
		   			window.open($ContextMenuTarget.find('.file-action').find('[data-action=share]').attr('href') , '_blank');
		   			break;
		   		case 'public':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=public]').click();
		   			break;
		   		case 'tag':
		   			$ContextMenuTarget.find('.file-action').find('[data-action=tag]').click();
		   			break;
		   	}
		}else if (ContextMenuType == 'multi') {
	   		switch (action) {
			   	case 'delete':
			   		$('#delete-btn').click();
			   		break;
			   	case 'move':
			   		$('#move-btn').click();
			   		break;
			   	case 'tag':
			   		$('#tag-btn').click();
			   		break;
		   	}
	   	}
	});

	/* Delete File*/
	$('body').on('click', '.file-action a[data-delete-id]', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'ajax/' + $(this).attr('href'),
			type: 'GET',
			dataType: 'json',
			data: {
				id: $(this).attr('data-delete-id')
			},
			success: function(data) {
				if (data.message != '成功刪除檔案。') {
					swal('刪除失敗', data.message, 'warning');
				}
				updateAll();
			},
			error: function() {
				swal('刪除失敗', '', 'warning');
			}
		});
	});

	$('body').on('click', '.file-action a[data-dir-delete-id]', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'ajax/' + $(this).attr('href'),
			type: 'GET',
			dataType: 'json',
			data: {
				id: $(this).attr('data-dir-delete-id')
			},
			success: function(data) {
				if (data.message != '成功刪除。') {
					swal('刪除失敗', data.message, 'warning');
				}
				updateAll();

			},
			error: function() {
				swal('刪除失敗', '', 'warning');
			}
		});
	});
	$('body').on('click', '.real_delete', function(e) {
		e.preventDefault();
		var $Target = $(this);
		swal({
		  title: '永久刪除',
		  text: '此行為無法回復，你確定要執行嗎？',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonClass: 'btn-danger',
		  confirmButtonText: '確定',
		  cancelButtonText: '取消',
		  closeOnConfirm: false
		},
		function() {
			var url = '';
			if ($Target.attr('data-type') == 'dir') {
				url = 'ajax/ajax_delete_dir.php';
			} else {
				url = 'ajax/ajax_delete.php';
			}
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				data: {
					id: $Target.attr('data-id')
				},
				error: function() {
					swal('檔案刪除失敗', '', 'warning');
				}
			});
			updateAll();
			swal('刪除完成', '該檔案已經被永久刪除', 'success');
		});

	});
	$('body').on('click', '#real_delete_all', function(e) {
		e.preventDefault();
		swal({
		  title: '永久刪除',
		  text: '此行為無法回復，你確定要執行嗎？',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonClass: 'btn-danger',
		  confirmButtonText: '確定',
		  cancelButtonText: '取消',
		  closeOnConfirm: false
		},
		function() {
			$('.real_delete').each(function() {
				if ($(this).attr('data-type') == 'dir') {
					url = 'ajax/ajax_delete_dir.php';
				} else {
					url = 'ajax/ajax_delete.php';
				}
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					data: {
						id: $(this).attr('data-id')
					},
					error: function() {
						swal('檔案刪除失敗', '', 'warning');
					}
				});
			});
			swal('刪除完成', '所有檔案已經被永久刪除', 'success');
			updateAll();
		});
	});

	$('body').on('click', '.recycle_back', function(e) {
		e.preventDefault();
		var url = '';
		if ($(this).attr('data-type') == 'dir') {
			url = 'ajax/ajax_recycle_back_dir.php';
		} else {
			url = 'ajax/ajax_recycle_back.php';
		}
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			data: {
				id: $(this).attr('data-id')
			},
			error: function() {
				swal('檔案還原失敗', '', 'warning');
			}
		});
		updateAll();

	});
	$('#delete-btn').on('click', function(e) {
		var url = '';
		$('.ui-selected').each(function() {
			if ($(this).attr('data-type') == 'dir') {
				url = 'ajax/ajax_recycle_dir.php';
			} else {
				url = 'ajax/ajax_recycle.php';
			}
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				data: {
					id: $(this).attr('data-id')
				},
				error: function() {
					swal('檔案刪除失敗', '', 'warning');
				}
			});
			updateAll();
		});
	});

	/* Rename File */
	$('#rename-file').on('hide.bs.modal', function() {
		$('#rename-file-btn').off();
	});
	$('body').on('click', '.file-action a[data-dir-color-id], .file-action a[data-color-id]', function(e) {
		e.preventDefault();
		$('.color-tag-btn.btn').removeClass('active');
		$('.color-tag-btn.btn').find('input').removeAttr('checked');
		if ($('.ui-selected').length == 1) {
			if ($(this).attr('data-dir-color-id') != null) {
				$('.color-tag-btn').attr('data-id', $(this).attr('data-dir-color-id')).attr('data-type', 'dir');
			} else {
				$('.color-tag-btn').attr('data-id', $(this).attr('data-color-id')).attr('data-type', 'file');
			}
			if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-success')) {
				$('.color-tag-btn.btn-success').addClass('active');
				$('.color-tag-btn.btn-success').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-warning')) {
				$('.color-tag-btn.btn-warning').addClass('active');
				$('.color-tag-btn.btn-warning').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-info')) {
				$('.color-tag-btn.btn-info').addClass('active');
				$('.color-tag-btn.btn-info').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-danger')) {
				$('.color-tag-btn.btn-danger').addClass('active');
				$('.color-tag-btn.btn-danger').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-primary')) {
				$('.color-tag-btn.btn-primary').addClass('active');
				$('.color-tag-btn.btn-primary').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-default')) {
				$('.color-tag-btn.btn-default').addClass('active');
				$('.color-tag-btn.btn-default').find('input').attr('checked', 'checked');
			}else if ($(this).parents('.panel').find('.panel-footer').hasClass('tag-black')) {
				$('.color-tag-btn.btn-black').addClass('active');
				$('.color-tag-btn.btn-black').find('input').attr('checked', 'checked');
			} else {
				$('.color-tag-btn.btn-default').addClass('active');
				$('.color-tag-btn.btn-default').find('input').attr('checked', 'checked');
			}
		}
		$('#color-tag').modal('show');
	});
	$('body').on('click', '.file-action a[data-rename-id]', function(e) {
		e.preventDefault();
		var filename = $(this).parents('.panel').find('.panel-footer').eq(0).text();
		var ext = filename.split('.').pop();
		var name = filename.replace('.' + ext, '');
		console.log(filename + ', ' + name + ', ' + ext);
		$(this).parents('.panel').removeClass('ui-selectee ui-selected').addClass('renaming').find('.panel-footer').attr('style', 'padding:6px 15px').html('<div class="input-group input-group-sm"><input class="form-control rename-input" data-rename-url="' + $(this).attr('href') + '" data-id="' + $(this).attr('data-rename-id') + '" value="' + name + '" /><div class="input-group-addon file-ext">.' + ext + '</div></div>');
		$('.rename-input').focus();
	});
	$('body').on('blur', '.rename-input', function() {
		$('.rename-input').focus();
	});
	$('body').on('keyup', '.rename-input', function(e) {
		if (e.keyCode == 27) {
			e.preventDefault();
			$('.rename-input').remove();
			updateAll();
			return false;
		}else if (e.type == 'keyup' && e.keyCode == 13) {
			e.preventDefault();
			if ($('.rename-input').val() == '') {
				updateAll();
				return false;
			}
			var val = $('.rename-input').val() + $('.file-ext').eq(0).text();
			if (val) {
				$.ajax({
					url: 'ajax/' + $('.rename-input').attr('data-rename-url'),
					type: 'GET',
					dataType: 'json',
					data: {
						id: $('.rename-input').attr('data-id'),
						name: val
					},
					success: function(data) {
						updateAll();

					},
					error: function() {
						swal('重新命名發生錯誤', '', 'warning');
					}
				});
			}
		}
	});
	$('body').on('click', '.file-action a[data-dir-rename-id]', function(e) {
		e.preventDefault();
		var name = $(this).parents('.panel').find('.panel-footer').eq(0).text();
		console.log(name);
		$(this).parents('.panel').removeClass('ui-selectee ui-selected').addClass('renaming').find('.panel-footer').attr('style', 'padding:6px 15px').find('span').html('<div class="input-group input-group-sm"><input class="form-control rename-input-dir" data-rename-url="' + $(this).attr('href') + '" data-id="' + $(this).attr('data-dir-rename-id') + '" value="' + name + '" /></div>');
		$('.rename-input-dir').focus();
	});
	$('body').on('blur', '.rename-input-dir', function() {
		$('.rename-input-dir').focus();
	});
	$('body').on('keydown', '.rename-input-dir', function(e) {
		if (e.keyCode === 27) {
			e.preventDefault();
			$('.rename-input-dir').remove();
			updateAll();
			return false;
		}else if (e.keyCode === 13) {
			if ($('.rename-input-dir').val() == '') {
				updateAll();
				return false;
			}
			$.ajax({
				url: 'ajax/' + $('.rename-input-dir').attr('data-rename-url'),
				type: 'GET',
				dataType: 'json',
				data: {
					id: $('.rename-input-dir').attr('data-id'),
					name: $('.rename-input-dir').val()
				},
				success: function(data) {
					updateAll();
				},
				error: function() {
					swal('重新命名發生錯誤', '', 'warning');
				}
			});
		}
	});

	/* File Tag */
	$('body').on('click', '#tag-btn', function(e) {
		e.preventDefault();
		$('#color-tag').modal('show');
	});
	$('body').on('click', '.color-tag-btn', function(e) {
		e.preventDefault();
		$(this).addClass('active');
		var id = $(this).attr('id');
		$('.ui-selected').each(function() {
			$.ajax({
				url: 'ajax/ajax_set_color.php',
				type: 'GET',
				dataType: 'json',
				data: {
					id: $(this).attr('data-id'),
					type: $(this).attr('data-type'),
					color: id
				},
				success: function() {
					updateAll();
				},
				error: function() {
					swal('檔案標記錯誤', '', 'warning');
				}
			});
		});
		$('.color-tag-btn.btn').removeClass('active');
		$('.color-tag-btn.btn').find('input').removeAttr('checked');

		$('#color-tag').modal('hide');
	});

	/* Share */
	$('body').on('click', '.file-action a[data-share-id]', function(e) {
		e.preventDefault();
		if ($(this).text() == '公開檔案' || $(this).text() == '公開資料夾') {
			var $target = $(this);
			console.log(getCookie('share_warning'));
			if (getCookie('share_warning') != 'no_warning') {
				swal({
				  title: '你確定要公開檔案嗎？',
				  text: '公開檔案會降低您檔案的安全係數，您確定要這麼作嗎？',
				  type: 'warning',
				  showCancelButton: true,
				  confirmButtonClass: 'btn-danger',
				  confirmButtonText: '恩，我確定要公開檔案',
				  cancelButtonText: '取消',
				  closeOnConfirm: false
				},function(isConfirm) {
					if (isConfirm) {
						$.ajax({
							url: 'ajax/ajax_share.php',
							type: 'GET',
							dataType: 'json',
							data: {
								id: $target.attr('data-share-id'),
								type: $target.attr('data-share-type'),
							},
							success: function(data) {
								updateAll();
							},
							error: function() {
								swal('改變權限發生錯誤', '', 'warning');
							}
						});
					  	swal({
						    title: '未來還需要提醒您嗎？',
						    text: '未來是否還要再跳此通知',
						    type: 'warning',
						    showCancelButton: true,
						    confirmButtonClass: 'btn-info',
						    confirmButtonText: '以後不需要再提醒我了',
						    cancelButtonText: '以後還是要提醒我',
						    closeOnConfirm: true
					  	},function() {
					  		setCookie('share_warning', 'no_warning', 365);
					  	});
					}else {
						return false;
					}
				});
			}else {
				$.ajax({
					url: 'ajax/ajax_share.php',
					type: 'GET',
					dataType: 'json',
					data: {
						id: $target.attr('data-share-id'),
						type: $target.attr('data-share-type'),
					},
					success: function(data) {
						updateAll();
					},
					error: function() {
						swal('改變權限發生錯誤', '', 'warning');
					}
				});
			}
		}else {
			$.ajax({
				url: 'ajax/ajax_share.php',
				type: 'GET',
				dataType: 'json',
				data: {
					id: $(this).attr('data-share-id'),
					type: $(this).attr('data-share-type'),
				},
				success: function(data) {
					updateAll();
				},
				error: function() {
					swal('改變權限發生錯誤', '', 'warning');
				}
			});
		}
	});

	$('#recycle-btn').on('click', function(e) {
		e.preventDefault();
		$('#recycle-box').modal('show');
	});
	$('#upload-file').on('hide.bs.modal', function() {
		$('#upload-file-btn').off();
	});
	$('#upload-btn').on('click', function(e) {
		e.preventDefault();
		$('#upload-file').modal('show');
	});
	$('#mkdir-btn').on('click', function(e) {
		e.preventDefault();
		$('#mkdir-modal').modal('show');
	});
	$('#info-btn').on('click', function(e) {
		e.preventDefault();
		$('#info-modal').modal('show');
	});

	/* Move File */
	$('#move-btn').on('click', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'ajax/ajax_list_dir.php',
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				$('#ajax_load_tree').html(data[0].content);
			},
		});
		$('#mvfile-modal').modal('show');
	});
	var moving_id = '';
	$('body').on('click', '.file-action a[data-move-id] , .file-action a[data-dir-move-id]', function(e) {
		e.preventDefault();
		if ($(this).attr('data-dir-move-id') != null) {
			moving_id = $(this).attr('data-dir-move-id');
			//$('tr[data-id="' + $(this).attr('data-dir-move-id') + '"]').addClass('is_moving');
			var cdir = $(this).attr('data-dir-move-id');
		} else {
			moving_id = $(this).attr('data-move-id');
			//$('tr[data-id="' + $(this).attr('data-move-id') + '"]').addClass('is_moving');
			var cdir = '';
		}
		console.log($(this).attr('data-move-id'));
		$.ajax({
			url: 'ajax/ajax_list_dir.php',
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				$('#ajax_load_tree').html(data[0].content);
				$('#ajax_load_tree').find('a').addClass('filetreeselector').removeAttr('href');
				//$('[data-id=' + cdir + ']').removeClass("filetreeselector");
				//$('[data-parent=' + cdir + ']').remove();
			},
		});
		$('#mvfile-modal').modal('show');
	});
	$('body').on('click', '.filetreeselector', function(e) {
		e.preventDefault();
		$('.filetreeselector').removeClass('tree_selected');
		$(this).addClass('tree_selected');
	});
	$('#move-submit-btn').on('click', function(e) {
		var url = '';
		if ($('.tree_selected').attr('data-id') != null) {
			$('.ui-selected').each(function() {
				if ($(this).attr('data-type') == 'dir') {
					url = 'ajax/ajax_move_dir.php';
				} else {
					url = 'ajax/ajax_move.php';
				}
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					data: {
						id: $(this).attr('data-id'),
						dir: $('.tree_selected').attr('data-id')
					},
					error: function() {
						swal('檔案移動失敗', '', 'warning');
					}
				});
			});
			updateAll();
			$('#mvfile-modal').modal('hide');
		}else {
			swal('您沒有選取資料夾', '', 'warning');
		}
	});
	/* Preview */
	$('body').on('click', '.file-action a[data-preview-id]', function(e) {
		e.preventDefault();
		$('#preview-iframe').attr('src', 'preview.php?id=' + $(this).attr('data-preview-id'));
		$('#preview-file').modal('show');
	});
	$('#preview-file').on('hide.bs.modal', function(e) {
		$('#preview-iframe').attr('src', 'preview.php');
	});

	/* File List & Select */
	$('#file_list_container').selectable({
		filter: '.panel',
		cancel: '.ui-selected, .renaming',
		start: function(e, ui ) {
			$contextMenu.hide();
		},
		selecting: function(e, ui) {
			$('#navbar-file-action').html('');
		}
	});

	$('body').on('mousedown', '#file_list_container .ui-selectee', function(e) {
		if (e.which == 1) {
			if (!e.ctrlKey && !e.metaKey) {
				$('.ui-selected').removeClass('ui-selected');
		        $('#file_list_container').trigger('selectableselected');
		    }
		    if (e.ctrlKey || e.metaKey) {
				$(this).removeClass('ui-selected');
		    }
		}
    });
	$('.ui-selected').on('click', function() {
  		$(this).removeClass('ui-selected').parents('.ui-selectable').trigger('selectablestop');
	});

	$('body').on('dblclick', '.panel:not(.renaming), .panel i', function(e) {
		if (!e.ctrlKey && !e.metaKey) {
			console.log($(this).parent().html());
			window.open($(this).attr('data-download-url') , '_self');
        }
    });

    $('body').on('click', '.file-action', function(e) {
    	e.stopPropagation();
    });

    /* Create Directory */
    $('#mkdir-submit-btn').on('click', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'ajax/ajax_mkdir.php',
			type: 'POST',
			dataType: 'json',
			data: {
				name: $('#mkdirname').val()
			},
			success: function(data) {
				if (!data.success) {
					swal('資料夾建立失敗', data.message, 'warning');
				}else{
					swal('資料夾建立完成', '', 'success');
				}
				updateAll();

			},
			error: function() {
				swal('資料夾建立失敗', '', 'warning');
			}
		});
	});

	/* Upload */
	if (window.FileReader && Modernizr.draganddrop) {
		$('#ajax_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#upload_box').show();
		$('#upload_progress_box').show();
		$('#uploadpercentagebox').show();
		$('#upload_table_box').show();
		$('#upload_iframe').hide();
		$('#remote_frame').hide();
	} else {
		$('#traditional_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#uploadpercentagebox').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').show();
		$('#remote_frame').hide();
	}
	$('body').on('click', '#ajax_upload_btn', function(e) {
		e.preventDefault();
		$('#ajax_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#traditional_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#remote_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').show();
		$('#upload_progress_box').show();
		$('#uploadpercentagebox').show();
		$('#upload_table_box').show();
		$('#upload_iframe').hide();
		$('#remote_frame').hide();
	});
	$('body').on('click', '#traditional_upload_btn', function(e) {
		e.preventDefault();
		$('#traditional_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#ajax_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#remote_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#uploadpercentagebox').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').show();
		$('#remote_frame').hide();
	});
	$('body').on('click', '#remote_upload_btn', function(e) {
		e.preventDefault();
		$('#remote_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#ajax_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#traditional_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#uploadpercentagebox').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').hide();
		$('#remote_frame').show();
	});
	var r = new Resumable({
	  	target:'uploadfile.php',
	  	testChunks: true,
	  	chunkSize: 1*1024*1024
	});
	var upload_list = [];
	r.assignBrowse(document.getElementById('file'));
	r.assignDrop(document.getElementById('upload_box'));
	r.on('fileAdded', function(file){
		$('#upload_progress').css('width', '0%').removeClass('text-success').removeClass('text-danger').addClass('text-info');
		r.upload();
	});
	r.on('progress', function(){
		$('#upload_progress').css('width', (r.progress() * 100) + '%');
		$('#uploadpercentage').text(Math.round(r.progress() * 100) + '%');
	});
	r.on('fileSuccess', function(file, message){
		console.log('finished!');
		console.log(message);
		var data = jQuery.parseJSON(message);
		console.log(data);
		console.log(file);
		upload_list.push(message);
		r.removeFile(file);

		//檢查是否已經全部上傳完成
		if(r.files.length == 0){
			console.log(upload_list);
			$('#uploadpercentage').text('加密中');

			for(var i=0; i < upload_list.length; i++){

				var data = jQuery.parseJSON(upload_list[i]);

				var check_encryption_status = setInterval(function(){
					$.ajax({
						url: 'temp/'+data.id+'.txt',
						type: 'GET',
						dataType: 'text',
						timeOut: 5,
						success: function(data){
							if ( data != ''  && $('#uploadpercentage').text() != '上傳完成！') {
								$('#uploadpercentage').text('加密進度：' + data);
								$('#upload_progress').css('width', data);
							}
						},
						error: function(data){
							if ( $('#uploadpercentage').text() != '上傳完成！') {
								$('#uploadpercentage').text('加密中');
							}
						}
					});
				}, 5000);

				$.ajax({
					url: 'encryptfile.php',
					type: 'POST',
					dataType: 'html',
					data: {
						file: data.id
					},
					success: function(dataa) {
						var result = "";
						if (data.result == 'success' && dataa == 'success') {
						    result = '上傳成功';
						    $('#uploadpercentage').text('上傳完成！');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-success');
						} else if (data.result == 'success' && dataa != 'success') {
						    result = '加密失敗';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'sizeout') {
						    result = '檔案太大';$('uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'unknow') {
						    result = '找不到該檔案，或是發生未知得錯誤';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'totalout') {
						    result = '帳戶空間不足';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'inierr') {
						    result = '檔案超過 POST 或是伺服器設定限制';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'par') {
						    result = '系統錯誤，檔案上傳不完全';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else if (data.result == 'nofile') {
						    result = '沒有選取的檔案';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						} else {
						    result = '發生未知的錯誤';
						    $('#uploadpercentage').text('上傳失敗');
						    $('#uploadpercentage').removeClass('text-info').addClass('text-danger');
						}
						$('#upload_table').append("<tr><td>" + data.name + "</td><td>" + data.size + "</td><td>" + result + "</td></tr>");
						clearInterval(check_encryption_status);
					},
					error: function() {
						swal('加密程序發生錯誤', '', 'warning');
						clearInterval(check_encryption_status);
					}
				});
			}
			updateAll();
		}

	});
	r.on('fileError', function(file, message){
		console.log(file);
		if (data.result == 'par') {
			file.retry();
			result = '檔案上傳不完全，正在嘗試重新上傳';
			$('#uploadpercentage').text('上傳失敗');
			$('#uploadpercentage').removeClass('text-info').addClass('text-danger');
		}else{
			result = '檔案上傳失敗';
			$('#uploadpercentage').text('上傳失敗');
			$('#uploadpercentage').removeClass('text-info').addClass('text-danger');
		}
	});
	$('#remotedownload-btn').on('click', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'remotedownload.php',
			data: {
				file: $('#remotedownload').val()
			},
			type: 'post',
			dataType: 'json',
			cache: false,
			success: function(data) {
				$.ajax({
					url: 'encryptfile.php',
					type: 'POST',
					dataType: 'html',
					data: {
						file: data.id
					},
					success: function(dataa) {
						var result = "";
						if (data.result == 'success' && dataa == 'success') {
						    result = '上傳成功';
						} else if (data.result == 'success' && dataa != 'success') {
						    result = '加密失敗';
						} else if (data.result == 'sizeout') {
						    result = '檔案太大';
						} else if (data.result == 'unknow') {
						    result = '找不到該檔案，或是發生未知得錯誤';
						} else if (data.result == 'totalout') {
						    result = '帳戶空間不足';
						} else if (data.result == 'inierr') {
						    result = '檔案超過 POST 或是伺服器設定限制';
						} else if (data.result == 'par') {
						    result = '系統錯誤，檔案上傳不完全';
						} else if (data.result == 'nofile') {
						    result = '沒有選取的檔案';
						} else {
						    result = '發生未知的錯誤';
						}
						$('#remote_table').append("<tr><td>" + data.name + "</td><td>" + data.size + "</td><td>" + result + "</td></tr>");
					},
					error: function() {
						swal('加密程序發生錯誤', '', 'warning');
					}
				});
				updateAll();
			},
			error: function() {
				swal('上傳程序發生錯誤', '', 'warning');
				updateAll();
			}
		});
	});
	$('div #upload_box').on('dragover', function(e) {
		e.preventDefault();
		$(this).addClass('dragover_box');
	});
	$('div #upload_box').on('dragleave', function(e) {
		e.preventDefault();
		$(this).removeClass('dragover_box');
	});
	$('div #upload_box').on('dragenter', function(e) {
		e.preventDefault();
		$(this).addClass('dragover_box');
	});
	$('div #upload_box').on('dragout', function(e) {
		e.preventDefault();
		$(this).removeClass('dragover_box');
	});

	/* 啟動 AJAX */
	updateAll();
	checkUpdate();
});
