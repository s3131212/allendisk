/*
Allen Disk 1.4
Copyright (C) 2012~2014 Allen Chou
Author: Allen Chou ( http://allenchou.cc )
License: MIT License
*/
var updateList = function(){
    $.ajax({
        url: 'ajax_filelist.php',
        type: 'GET',
        dataType: 'html',
        cache: false,
        data:{
            dir: dir
        },
        success: function(data){
            $('#file_list_container').html(data);
            $('.tooltip , .tooltip-arrow').remove();
        }
    });
};
var updateRecycleList = function(){
    $.ajax({
        url: 'ajax_recyclelist.php',
        type: 'GET',
        dataType: 'html',
        cache: false,
        success: function(data){
            $('#recycle_list').html(data);
        }
    });
};
var updateUsage = function(){
    $.ajax({
        url: 'ajax_list_usage.php',
        type: 'GET',
        dataType: 'html',
        cache: false,
        success: function(data){
            $('#usage_box').html(data);
        }
    });
};
var update = true;
$(function(){
    function progressload(e){
        if(e.lengthComputable){
            $('#upload_progress').css("width",(e.loaded/e.total)*100+'%');
            $('#upload_progress').text(Math.round(e.loaded/e.total*10000)/100+'%');
        }
    }
    $('body').on('click', '.tree-indicator', function(){
		var $state = $(this).html();
		$(this).parent().find('ul.dir-tree').slideToggle(500);
		$(this).html($state == '<i class="ion-ios7-plus-empty"></i>' ? '<i class="ion-ios7-minus-empty"></i>' : '<i class="ion-ios7-plus-empty"></i>');
	});

    $('#rename-file').modal({
        show: false
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

    $('#rename-dir').modal({
        show: false
    });
    $('#recycle-box').modal({
        show: false
    });
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    $('body').on('click', '.file-actions a[data-delete-id]', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            dataType: 'json',
            data: {
                id: $(this).attr('data-delete-id')
            },
            success: function(data){
                if(data.message != "成功刪除檔案。"){
                window.alert(data.message);
                }
                updateList();
            },
            error: function(){
                window.alert("檔案刪除時發生錯誤！");
            }
        });
    });

    $('body').on('click', '.file-actions a[data-dir-delete-id]', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            dataType: 'json',
            data: {
                id: $(this).attr('data-dir-delete-id')
            },
            success: function(data){
                if(data.message != "成功刪除。"){
                    window.alert(data.message);
                }
                updateList();
            },
            error: function(){
                window.alert("檔案刪除時發生錯誤！");
            }
        });
    });

	$('#rename-file').on('hide.bs.modal', function(){
		$('#rename-file-btn').off();
	});
    $('body').on('click', '.file-actions a[data-dir-color-id], .file-actions a[data-color-id]', function(e){
        e.preventDefault();
        if($(this).attr('data-dir-color-id')!=null){
            $('.color-tag-btn').attr('data-id',$(this).attr('data-dir-color-id')).attr('data-type','dir');
        }else{
            $('.color-tag-btn').attr('data-id',$(this).attr('data-color-id')).attr('data-type','file');
        }
        $('#color-tag').modal('show');
    });
    $('body').on('click', '#tag-btn', function(e){
        e.preventDefault();
        $('#color-tag').modal('show');
    });
    $('body').on('click', '.color-tag-btn', function(e){
        e.preventDefault();
        if($('#multi-btn').hasClass('active')){
            var id = $(this).attr('id');
            $( ".checkbox_file:checked" ).each(function() {
                $.ajax({
                    url: 'ajax_set_color.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: $(this).attr('data-id'),
                        type: $(this).attr('data-type'),
                        color: id
                    },
                    error: function(){
                        window.alert("檔案標記錯誤");
                    }
                });
                updateList();
                $('#multi-btn').removeClass("active");
                $('thead tr .checkbox_box').remove();
                $('tbody tr .checkbox_box').remove();
                $('#action_btn_multi').hide();
                update = true;
            });
        }else{
            $.ajax({
                url: 'ajax_set_color.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-id'),
                    type: $(this).attr('data-type'),
                    color: $(this).attr('id')
                },
                success: function(data){
                    window.alert(data.message);
                    updateList();
                },
                error: function(){
                    window.alert("更新失敗");
                }
            });
            updateList();
        }
    });
    $('body').on('click', '.file-actions a[data-rename-id]', function(e){
        e.preventDefault();
        var self = this;
        var name = $('tr[data-id=' + $(this).attr('data-rename-id') + '] td').eq(0).text();
		var ext = name.split('.').pop();
		var file = name.replace('.' + ext, '');
        
		$('#rename-filename').val(file);
        $('#rename-file-ext').text('.' + ext);
        $('#rename-file').modal('show');
        $('#rename-file-btn').one('click', function(){
            var val = $('#rename-filename').val() + '.' + ext;
            if(val){
                $.ajax({
                    url: $(self).attr('href'),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: $(self).attr('data-rename-id'),
                        name: val
                    },
                    success: function(data){
                        window.alert(data.message);
                        updateList();
                    },
                    error: function(){
                        window.alert("重新命名時發生錯誤！");
                    }
                });
            }
            else {
                window.alert("請輸入檔名！");
            }
        })
    });
    $('body').on('click', '.file-actions a[data-share-id]', function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax_share.php',
            type: 'GET',
            dataType: 'json',
            data: {
                id: $(this).attr('data-share-id'),
                type: $(this).attr('data-share-type'),
            },
            success: function(data){
                updateList();
            },
            error: function(){
                window.alert("權限改變時發生錯誤");
            }
        });
    });
    $('body').on('click', '.file-actions a[data-dir-rename-id]', function(e){
        e.preventDefault();
        var self = this;
        var file = $('tr[data-id=' + $(this).attr('data-dir-rename-id') + '] td').eq(0).text();
        $('#rename-dirname').val(file);
        $('#rename-dir').modal('show');
        $('#rename-dir-btn').one('click', function(){
            if($('#rename-dirname').val()!=null){
                $.ajax({
                    url: $(self).attr('href'),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: $(self).attr('data-dir-rename-id'),
                        name: $('#rename-dirname').val()
                    },
                    success: function(data){
                        window.alert(data.message);
                        updateList();
                    },
                    error: function(){
                        window.alert("重新命名時發生錯誤！");
                    }
                });
            }else {
                window.alert("請輸入名稱！");
            }
        })
    });
    $('#recycle-btn').on('click',function(e){
        e.preventDefault();
        updateRecycleList();
        $('#recycle-box').modal('show');
    });
    $('#upload-file').on('hide.bs.modal', function(){
        $('#upload-file-btn').off();
    });
    $('#upload-btn').on('click', function(e){
        e.preventDefault();
        $('#upload-file').modal('show');
    });
    $('#mkdir-btn').on('click', function(e){
        e.preventDefault();
        $('#mkdir-modal').modal('show');
    });
    $('#info-btn').on('click', function(e){
        e.preventDefault();
        $('#info-modal').modal('show');
    });
    $('#move-btn').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax_list_dir.php',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                $('#ajax_load_tree').html(data[0].content);
            },
        });
        update = false;
        $('#mvfile-modal').modal('show');
    });
    $('body').on('click', '.file-actions a[data-preview-id]', function(e){
        e.preventDefault();
        $('#preview-iframe').attr('src','preview.php?id='+$(this).attr('data-preview-id'));
        $('#preview-file').modal('show');
    });
    $('#preview-file').on('hide.bs.modal', function (e) {
    	$('#preview-iframe').attr('src','preview.php');
	})
    $('body').on('click', '.file-actions a[data-move-id] , .file-actions a[data-dir-move-id]', function(e){
        e.preventDefault();
        update = false;
        if($(this).attr('data-dir-move-id')!=null){
            $('tr[data-id="' + $(this).attr('data-dir-move-id') + '"]').addClass('is_moving');
            var cdir = $(this).attr('data-dir-move-id');
        }else{
            $('tr[data-id="' + $(this).attr('data-move-id') + '"]').addClass('is_moving');
            var cdir = "";
        }
        $.ajax({
            url: 'ajax_list_dir.php',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                $('#ajax_load_tree').html(data[0].content);
                $('[data-id='+cdir+']').removeClass("filetreeselector");
                $('[data-parent='+cdir+']').remove();
            },
        });
        $('#mvfile-modal').modal('show');
    });
    $('body').on('click','.real_delete',function(e){
        e.preventDefault();
        if(window.confirm('確定永久刪除？此舉動無法回復')){
            var url = "";
            if($(this).attr('data-type')=="dir"){
                url = "ajax_delete_dir.php";
            }else{
                url = "ajax_delete.php";
            }
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-id')
                },
                error: function(){
                    window.alert("檔案刪除時發生錯誤！");
                }
            });
        }
        updateList();
        updateRecycleList();
    });
    $('body').on('click','#real_delete_all',function(e){
        e.preventDefault();
        if(window.confirm('確定清除所有檔案？此舉動無法回復')){
            var url = "";
            $( ".real_delete" ).each(function() {
                if($(this).attr('data-type')=="dir"){
                    url = "ajax_delete_dir.php";
                }else{
                    url = "ajax_delete.php";
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: $(this).attr('data-id')
                    },
                    error: function(){
                        window.alert("檔案刪除時發生錯誤！");
                    }
                });
            });
        }
        updateList();
        updateRecycleList();
    });

    $('body').on('click','.recycle_back',function(e){
        e.preventDefault();
        var url = "";
        if($(this).attr('data-type')=="dir"){
            url = "ajax_recycle_back_dir.php";
        }else{
            url = "ajax_recycle_back.php";
        }
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {
                id: $(this).attr('data-id')
            },
            error: function(){
                window.alert("檔案還原時發生錯誤！");
            }
        });
        updateList();
        updateRecycleList();
    });
    $('body').on('click', '.filetreeselector', function(e){
        e.preventDefault();
        $('.filetreeselector').removeClass("tree_selected");
        $(this).addClass("tree_selected");
    });
    $('#multi-btn').on('click',function(e){
        e.preventDefault();
        update = false;
        if(!$('#multi-btn').hasClass("active")){
            update = false;
            $('#multi-btn').addClass("active");
            $('#file-list thead tr').prepend('<td class="checkbox_box"></td>');
            $('#file-list tbody tr[data-id]').each(function(){
                $(this).prepend('<td class="checkbox_box"><input style="margin:0 !important; position: relative;" type="checkbox" name="selected[]" class="checkbox_file" data-id="'+ $(this).attr('data-id') +'" data-type="'+ $(this).attr('data-type') +'"></td>');
            });
            $('#action_btn_multi').show();
        }else{
            update = true;
            $('#multi-btn').removeClass("active");
            $('#file-list thead tr .checkbox_box').remove();
            $('#file-list tbody tr .checkbox_box').remove();
            $('#action_btn_multi').hide();
        }
        
    });
    $('#delete-btn').on('click',function(e){
        var url = "";
        $( ".checkbox_file:checked" ).each(function() {
            if($(this).attr('data-type')=="dir"){
                url = "ajax_recycle_dir.php";
            }else{
                url = "ajax_recycle.php";
            }
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-id')
                },
                error: function(){
                    window.alert("檔案刪除時發生錯誤！");
                }
            });
        });
        updateList();
        $('#multi-btn').removeClass("active");
        $('thead tr .checkbox_box').remove();
        $('tbody tr .checkbox_box').remove();
        $('#action_btn_multi').hide();
        update = true;
    });
    $('#select-all-btn').on('click',function(){
        if($(this).text()=='全選'){
            $(this).text('取消全選');
            $( ".checkbox_file" ).each(function() {
                $(this).prop('checked',true);
            });
        }else{
            $(this).text('全選');
            $( ".checkbox_file" ).each(function() {
                $(this).prop('checked',false);
            });
        }
    });
    $('#move-submit-btn').on('click',function(e){
        var url = "";
        if($('#multi-btn').hasClass("active")){
            $( ".checkbox_file:checked" ).each(function() {
                if($(this).attr('data-type')=="dir"){
                    url = "ajax_move_dir.php";
                }else{
                    url = "ajax_move.php";
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: $(this).attr('data-id'),
                        dir: $('.tree_selected').attr('data-id')
                    },
                    error: function(){
                        window.alert("檔案移動時發生錯誤！");
                    }
                });
            });
            $('#multi-btn').removeClass("active");
            $('thead tr .checkbox').remove();
            $('tbody tr .checkbox').remove();
            $('#action_btn_multi').hide();
            update = true;
        }else{
            if($('.is_moving').attr('data-type')=="dir"){
                url = "ajax_move_dir.php";
            }else{
                url = "ajax_move.php";
            }
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $('.is_moving').attr('data-id'),
                    dir: $('.tree_selected').attr('data-id')
                },
                error: function(){
                    window.alert("檔案移動時發生錯誤！");
                }
            });
        }
        update = true;
        updateList();
        $('#mvfile-modal').modal('hide');
        
    });
    //Upload
    if (window.FileReader && Modernizr.draganddrop){
        $('#ajax_upload_btn').removeClass("btn-info").addClass("btn-primary");
        $('#upload_box').show();
        $('#upload_progress_box').show();
        $('#upload_table_box').show();
        $('#upload_iframe').hide();
        $('#remote_iframe').hide();
    }else{
        $('#traditional_upload_btn').removeClass("btn-info").addClass("btn-primary");
        $('#upload_box').hide();
        $('#upload_progress_box').hide();
        $('#upload_table_box').hide();
        $('#upload_iframe').show();
        $('#remote_iframe').hide();
    }
    $('body').on('click', '#ajax_upload_btn', function(e){
        e.preventDefault();
        $('#ajax_upload_btn').removeClass("btn-info").addClass("btn-primary");
        $('#traditional_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#remote_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#upload_box').show();
        $('#upload_progress_box').show();
        $('#upload_table_box').show();
        $('#upload_iframe').hide();
        $('#remote_iframe').hide();
    });
    $('body').on('click', '#traditional_upload_btn', function(e){
        e.preventDefault();
        $('#traditional_upload_btn').removeClass("btn-info").addClass("btn-primary");
        $('#ajax_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#remote_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#upload_box').hide();
        $('#upload_progress_box').hide();
        $('#upload_table_box').hide();
        $('#upload_iframe').show();
        $('#remote_iframe').hide();
    });
    $('body').on('click', '#remote_upload_btn', function(e){
        e.preventDefault();
        $('#remote_upload_btn').removeClass("btn-info").addClass("btn-primary");
        $('#ajax_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#traditional_upload_btn').removeClass("btn-primary").addClass("btn-info");
        $('#upload_box').hide();
        $('#upload_progress_box').hide();
        $('#upload_table_box').hide();
        $('#upload_iframe').hide();
        $('#remote_iframe').show();
    });
    $('#upload_box').on('drop',function(e){
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        var length = e.originalEvent.dataTransfer.files.length;
        for (var i = 0; i < length; i++) {
            $('#upload_progress').css("width",'0%');
            var formData = new FormData(); 
            formData.append('file[]',files[i]);
            $.ajax({
                url:"uploadfile.php",
                data:formData,
                type:'post',
                dataType: 'html',
                processData: false, //important!
                contentType: false,
                cache: false,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){
                        myXhr.upload.addEventListener('progress',progressload, false);
                    }
                    return myXhr;
                },
                success: function(data){
                    $('#upload_table').append(data);
                    updateList();
                },
                error: function(){
                    window.alert("上傳錯誤");
                }
            });
        }
    });
    $("#file:file").on('change',function(e){
        e.preventDefault();
        $('#upload_progress').css("width",'0%');
        var files = this.files;
        var length = $(this)[0].files.length;
        for (var i = 0; i < length; i++) {
            $('#upload_progress').css("width",'0%');
            var uploadData = new FormData(); 
            uploadData.append('file[]',files[i]);
            $.ajax({
                url:"uploadfile.php",
                data:uploadData,
                type:'post',
                dataType: 'html',
                processData: false, //important!
                contentType: false,
                cache: false,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){
                        myXhr.upload.addEventListener('progress',progressload, false);
                    }
                    return myXhr;
                },
                success: function(data){
                    $('#upload_table').append(data);
                    updateList();
                },
                error: function(){
                    window.alert("上傳錯誤");
                }
            });
        }
     });
    $('div #upload_box').on('dragover',function(e){ 
        e.preventDefault();
        $(this).addClass("dragover_box");
    });
    $('div #upload_box').on('dragleave',function(e){
        e.preventDefault();
        $(this).removeClass("dragover_box");
    });
    $('div #upload_box').on('dragenter',function(e){
        e.preventDefault();
        $(this).addClass("dragover_box");
    });
    $('div #upload_box').on('dragout',function(e){
        e.preventDefault();
        $(this).removeClass("dragover_box");
    });
});