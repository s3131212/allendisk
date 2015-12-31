/*
Allen Disk 1.6
Copyright (C) 2012~2016 Allen Chou
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
	console.log(obj.contentWindow.document.body.scrollHeight);
	if (height === 'undefined' || height / $(window).height() < 0.5) {
		height = $(window).height() * 0.7;
	}
	console.log(height);
    obj.style.height = height + 'px';
}
$(function() {
	sweetAlertInitialize();
	function progressload(e) {
		if (e.lengthComputable) {
			$('#upload_progress').css('width', (e.loaded / e.total) * 100 + '%');
		}
	}

	function updateList() {
		$.ajax({
			url: 'ajax/ajax_filelist.php',
			type: 'GET',
			dataType: 'html',
			cache: false,
			timeout: 30000,
			data: {
				dir: dir
			},
			success: function(data) {
				$('#file_list_container').html(data);
			}
		});
	};

	function updateRecycleList() {
		$.ajax({
			url: 'ajax/ajax_recyclelist.php',
			type: 'GET',
			dataType: 'html',
			cache: false,
			timeout: 30000,
			success: function(data) {
				$('#recycle_list').html(data);
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
		   	if ($ContextMenuTarget.find('.file-action').find(':nth-child(7)').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=preview]').parent().remove();
		   	}

		   	//check link
		   	if ($ContextMenuTarget.find('.file-action').find(':nth-child(3)').hasClass('disabled')) {
		   		$('#contextMenu').find('a[data-action=link]').parent().remove();
		   	}

		   	//check share
		   	if ($ContextMenuTarget.find('.file-action').find(':nth-child(1)').hasClass('disabled')) {
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
		   	if ($ContextMenuTarget.find('.file-action').find(':nth-child(5)').hasClass('disabled')) {
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
		   			window.open($ContextMenuTarget.find('.file-action').find(':nth-child(1)').attr('href') , '_blank');
		   			break;
		   		case 'public':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(2)').click();
		   			break;
		   		case 'link':
		   			window.open($ContextMenuTarget.find('.file-action').find(':nth-child(3)').attr('href') , '_blank');
		   			break;
		   		case 'delete':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(4)').click();
		   			break;
		   		case 'rename':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(5)').click();
		   			break;
		   		case 'move':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(6)').click();
		   			break;
		   		case 'preview':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(7)').click();
		   			break;
		   		case 'tag':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(8)').click();
		   			break;
		   	}
	   	}else if (ContextMenuType == 'dir') {
	   		switch (action) {
		   		case 'open':
					window.open($ContextMenuTarget.find(':nth-child(1)').attr('data-download-url') , '_self');
		   			break;
		   		case 'delete':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(2)').click();
		   			break;
		   		case 'rename':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(3)').click();
		   			break;
		   		case 'move':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(4)').click();
		   			break;
		   		case 'share':
		   			window.open($ContextMenuTarget.find('.file-action').find(':nth-child(5)').attr('href') , '_blank');
		   			break;
		   		case 'public':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(6)').click();
		   			break;
		   		case 'tag':
		   			$ContextMenuTarget.find('.file-action').find(':nth-child(7)').click();
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

		console.log(4);
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
	$('body').on('click', '.file-action a[data-preview-id]', function(e) {
		e.preventDefault();
		$('#preview-iframe').attr('src', 'preview.php?id=' + $(this).attr('data-preview-id'));
		$('#preview-file').modal('show');
	});
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

	$('#preview-file').on('hide.bs.modal', function(e) {
		$('#preview-iframe').attr('src', 'preview.php');
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
	$('body').on('click', '.filetreeselector', function(e) {
		e.preventDefault();
		$('.filetreeselector').removeClass('tree_selected');
		$(this).addClass('tree_selected');
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

	//$('#file_list_container').css('height', $(document.body).height() + 'px');

	//Upload
	if (window.FileReader && Modernizr.draganddrop) {
		$('#ajax_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#upload_box').show();
		$('#upload_progress_box').show();
		$('#upload_table_box').show();
		$('#upload_iframe').hide();
		$('#remote_iframe').hide();
	} else {
		$('#traditional_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').show();
		$('#remote_iframe').hide();
	}
	$('body').on('click', '#ajax_upload_btn', function(e) {
		e.preventDefault();
		$('#ajax_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#traditional_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#remote_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').show();
		$('#upload_progress_box').show();
		$('#upload_table_box').show();
		$('#upload_iframe').hide();
		$('#remote_iframe').hide();
	});
	$('body').on('click', '#traditional_upload_btn', function(e) {
		e.preventDefault();
		$('#traditional_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#ajax_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#remote_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').show();
		$('#remote_iframe').hide();
	});
	$('body').on('click', '#remote_upload_btn', function(e) {
		e.preventDefault();
		$('#remote_upload_btn').removeClass('btn-info').addClass('btn-primary');
		$('#ajax_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#traditional_upload_btn').removeClass('btn-primary').addClass('btn-info');
		$('#upload_box').hide();
		$('#upload_progress_box').hide();
		$('#upload_table_box').hide();
		$('#upload_iframe').hide();
		$('#remote_iframe').show();
	});
	$('#upload_box').on('drop', function(e) {
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		var length = e.originalEvent.dataTransfer.files.length;
		for (var i = 0; i < length; i++) {
			$('#upload_progress').css('width', '0%');
			var formData = new FormData();
			formData.append('file[]', files[i]);
			$.ajax({
				url: 'uploadfile.php',
				data: formData,
				type: 'post',
				dataType: 'html',
				processData: false, //important!
				contentType: false,
				cache: false,
				xhr: function() {
					var myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload) {
						myXhr.upload.addEventListener('progress', progressload, false);
					}
					return myXhr;
				},
				success: function(data) {
					$('#upload_table').append(data);
					updateAll();
				},
				error: function() {
					swal('上傳程序發生錯誤', 'warning');
				}
			});
		}
	});
	$('#file:file').on('change', function(e) {
		e.preventDefault();
		$('#upload_progress').css('width', '0%');
		var files = this.files;
		var length = $(this)[0].files.length;
		for (var i = 0; i < length; i++) {
			$('#upload_progress').css('width', '0%');
			var uploadData = new FormData();
			uploadData.append('file[]', files[i]);
			$.ajax({
				url: 'uploadfile.php',
				data: uploadData,
				type: 'post',
				dataType: 'html',
				processData: false, //important!
				contentType: false,
				cache: false,
				xhr: function() {
					var myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload) {
						myXhr.upload.addEventListener('progress', progressload, false);
					}
					return myXhr;
				},
				success: function(data) {
					$('#upload_table').append(data);
					updateAll();
				},
				error: function() {
					swal('上傳程序發生錯誤', '', 'warning');
				}
			});
		}
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
