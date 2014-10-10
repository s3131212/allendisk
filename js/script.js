$(function(){
    var update = true;
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
            }
        });
    };
    function progressload(e){
        if(e.lengthComputable){
            $('#upload_progress').css("width",(e.loaded/e.total)*100+'%');
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

    $('#upload-file').modal({
        show: false
    });

    $('#rename-dir').modal({
        show: false
    });
    $('body').on('click', '.file-actions a[data-delete-id]', function(e){
        e.preventDefault();
        if(window.confirm('確定刪除？')){
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-delete-id')
                },
                success: function(data){
                    window.alert(data.message);
                    updateList();
                },
                error: function(){
                    window.alert("檔案刪除時發生錯誤！");
                }
            });
        }
    });

    $('body').on('click', '.file-actions a[data-dir-delete-id]', function(e){
        e.preventDefault();
        if(window.confirm('確定刪除？')){
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).attr('data-dir-delete-id')
                },
                success: function(data){
                    window.alert(data.message);
                    updateList();
                },
                error: function(){
                    window.alert("檔案刪除時發生錯誤！");
                }
            });
        }
    });

	$('#rename-file').on('hide.bs.modal', function(){
		$('#rename-file-btn').off();
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
    $('#preview-file').on('hidden.bs.modal', function (e) {
    	$('#preview-iframe').attr('src','preview.php');
	})
    $('body').on('click', '.file-actions a[data-move-id] , .file-actions a[data-dir-move-id]', function(e){
        e.preventDefault();
        update = false;
        $.ajax({
            url: 'ajax_list_dir.php',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                $('#ajax_load_tree').html(data[0].content);
            },
        });
        if($(this).attr('data-dir-move-id')!=null){
            $('tr[data-id="' + $(this).attr('data-dir-move-id') + '"]').addClass('is_moving');
        }else{
            $('tr[data-id="' + $(this).attr('data-move-id') + '"]').addClass('is_moving');
        }
        $('#mvfile-modal').modal('show');
    });
    $('body').on('click', '.filetreeselector', function(e){
        e.preventDefault();
        $('.filetreeselector').removeClass("tree_selected");
        $(this).addClass("tree_selected");
    });
    $('#multi-btn').on('click',function(e){
        e.preventDefault();
        if(!$('#multi-btn').hasClass("active")){
            update = false;
            $('#multi-btn').addClass("active");
            $('thead tr').prepend('<td class="checkbox"></td>');
            $('tbody tr[data-id]').each(function(){
                $(this).prepend('<td class="checkbox"><input type="checkbox" name="selected[]" class="checkbox_file" data-id="'+ $(this).attr('data-id') +'" data-type="'+ $(this).attr('data-type') +'"></td>');
            })
            $('#action_btn_multi').show();
        }else{
            update = true;
            $('#multi-btn').removeClass("active");
            $('thead tr .checkbox').remove();
            $('tbody tr .checkbox').remove();
            $('#action_btn_multi').hide();
        }
        
    });
    $('#delete-btn').on('click',function(e){
        if(window.confirm('確定刪除？')){
            var url = "";
            $( ".checkbox_file:checked" ).each(function() {
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
            updateList();
            $('#multi-btn').removeClass("active");
            $('thead tr .checkbox').remove();
            $('tbody tr .checkbox').remove();
            $('#action_btn_multi').hide();
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
        $('#upload_box').show();
        $('#upload_progress_box').show();
        $('#upload_table_box').show();
        $('#upload_iframe').hide();
    }else{
        $('#upload_box').hide();
        $('#upload_progress_box').hide();
        $('#upload_table_box').hide();
        $('#upload_iframe').show();
    }
    $('body').on('click', '#ajax_upload_btn', function(e){
        e.preventDefault();
        $('#upload_box').show();
        $('#upload_progress_box').show();
        $('#upload_table_box').show();
        $('#upload_iframe').hide();
    });
    $('body').on('click', '#traditional_upload_btn', function(e){
        e.preventDefault();
        $('#upload_box').hide();
        $('#upload_progress_box').hide();
        $('#upload_table_box').hide();
        $('#upload_iframe').show();
    });
    $('#upload_box').on('drop',function(e){
        e.preventDefault();
        $('#upload_progress').css("width",'0%');
        var files = e.originalEvent.dataTransfer.files;
        var formData = new FormData(); 
        var length = e.originalEvent.dataTransfer.files.length;
        for (var i = 0; i < length; i++) {
            formData.append('file[]',files[i]);
        }
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
            },
            error: function(){
                window.alert("上傳錯誤");
            }
        });
    });
    $("#file:file").on('change',function(e){
        e.preventDefault();
        $('#upload_progress').css("width",'0%');
        var files = this.files;
        var uploadData = new FormData(); 
        for(var i = 0 ; i < files.length; i++) {
            uploadData.append('file[]',files[i]);
        }
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
            },
            error: function(){
                window.alert("上傳錯誤，請嘗試「傳統上傳」");
            }
        });
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
    setInterval(function(){
        if(update===true){
            updateList();
        }
    }, 1000);
});