<!--{use_layout layout/main}-->
<?php
$head_info = [
    'title' => '查看文件',
];
?>


<h4 class="mb-3"><?php echo $project_info['project_name']; ?></h4>
<h6 class="mb-3">
    目录路径：
    <a href="/static_file/file_list?project_key=<?php echo $project_info['project_key']; ?>&from_dir=<?php echo DIRECTORY_SEPARATOR; ?>">
        <?php echo DIRECTORY_SEPARATOR; ?>
    </a>
    <?php $temp=DIRECTORY_SEPARATOR; foreach($dir_arr as $filename): ?>
        <a class="ml-1" href="/static_file/file_list?project_key=<?php echo $project_info['project_key']; ?>&from_dir=<?php echo $temp.$filename; ?>">
            <?php $temp .= $filename.DIRECTORY_SEPARATOR; echo $filename.DIRECTORY_SEPARATOR; ?>
        </a>
    <?php endforeach; ?>
</h6>

<div class="table-responsive">
    <table class="table table-striped table-sm table_list" id="data_list">
        <tbody>
        <?php foreach($file_list as $key => $filename): ?>
        <tr _type="<?php echo is_integer($key)?'f':'d'; ?>" _filename="<?php echo $filename; ?>">
            <td>
                <?php if (is_integer($key)): ?>
                <?php echo $filename; ?>
                <?php else: ?>
                <a href="/static_file/file_list?project_key=<?php echo $project_info['project_key']; ?>&from_dir=<?php echo $from_dir.$filename; ?>">
                    <?php echo $filename; ?>
                </a>
                <?php endif; ?>
            </td>
            <td>
                <?php if (is_integer($key)): ?>
                    <a class="mr-3 upload" href="/static_file/upload_file">
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </a>
                    <a class="mr-1" href="/static_file/version_list?project_key=<?php echo $project_info['project_key']; ?>&source_path=<?php echo $from_dir.$filename; ?>">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>
                <?php else: ?>
                    <a class="mr-3 upload" href="/static_file/upload_folder">
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </a>
                    <a class="mr-1" href="/static_file/version_list?project_key=<?php echo $project_info['project_key']; ?>&source_path=<?php echo $from_dir.$filename.DIRECTORY_SEPARATOR; ?>">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>

        <?php if(empty($file_list)): ?>
            <tr>
                <td colspan="7" class="pt-5 pb-5"><center><?php echo $app->lang('no_data'); ?></center></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<script>
var currentDir = "<?php echo $from_dir; ?>", project_key="<?php echo $project_info['project_key']; ?>";

    $("#data_list").click(function(e){
        var obj = null;
        if(e.target.tagName.toLocaleUpperCase() == "A"){
            obj = $(e.target);
        }
        else if(e.target.parentNode.tagName.toLocaleUpperCase() == "A"){
            obj = $(e.target.parentNode);
        }

        if(obj!==null) {
            if (obj.hasClass("upload")) {
                e.preventDefault();
                type = obj.parents("tr").attr("_type");
                filename = obj.parents("tr").attr("_filename");
                url = obj.attr("href");
                var params = {
                    dtype:"json"
                    ,project_key: project_key
                };
                if (type=='d'){
                    params.dir_path = currentDir+filename;
                    $(document).dialog({
                        type : 'confirm',
                        closeBtnShow: true,
                        contentScroll: false,
                        titleText: "提示",
                        content: "<div class='text-left'>点击\"确定\"按钮后，目录："+currentDir+filename+" 下的所有文件将会被上传到阿里云：<br>" +
                            "按原文件名上传；<br>" +
                            "不递归目录；<br>" +
                            "不生成shtml文件；<br></div>",
                        onClickConfirmBtn: function(){
                            var toast = loading();
                            $.ajax({
                                type: 'post'
                                , dataType: 'json'
                                , url: url
                                , data: params
                                , success: function (data, textStatus, jqXHR) {
                                    toast.close();
                                    if (data.code == 0) {
                                        content = '<div class="text-left"><div>上传目录下的文件：'+params.dir_path+'</div>' +
                                            '<div>上传成功的文件有'+data.data.succ_file.length+'个：</div>';
                                        $.each(data.data.succ_file, function (index, item) {
                                            content += '<div>'+item+'</div>';
                                        });
                                        content += '<div>上传失败的文件有'+data.data.error_file.length+'个：</div>';
                                        $.each(data.data.error_file, function (index, item) {
                                            content += '<div>'+item+'</div>';
                                        });
                                        content += '</div>';
                                        $(document).dialog({
                                            titleText: "上传完成"
                                            ,content: content
                                            ,contentScroll: false
                                        });
                                    }
                                    else {
                                        $(document).dialog({
                                            type: "notice"
                                            , position: "bottom"
                                            , dialogClass: "dialog_warn"
                                            , infoText: data.msg
                                            , autoClose: 3000
                                            , overlayShow: false
                                        });
                                    }
                                }
                                , error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    toast.close();
                                    $(document).dialog({
                                        type: "notice"
                                        ,position: "bottom"
                                        ,dialogClass:"dialog_red"
                                        ,infoText: textStatus
                                        ,autoClose: 3000
                                        ,overlayShow: false
                                    });
                                }
                            });
                        }
                    });
                }
                else {
                    params.file_path = currentDir+filename;
                    var content = '<div class="text-left"><div>上传文件 '+params.file_path+' 至阿里云</div>' +
                        '<div><label><input type="checkbox" id="rename_file" class="mt-2" value="1" checked> 重命名文件</label></div>';
                    var suffix = filename.substring(filename.lastIndexOf(".")+1).toLowerCase();
                    if (suffix=="css" || suffix=="js"){
                        content += '<div><label><input type="checkbox" id="dev_model" class="mt-2" value="1"> 开发模式' +
                            '（不上传文件至阿里云、引用源文件）</label><div>'+
                            '<div class="input-group mt-2"><div class="input-group-prepend">\n' +
                            '                  <span class="input-group-text pl-2 pr-1">/include/</span>\n' +
                            '                </div><input type="text" id="shtml_path" class="form-control" ' +
                            'placeholder="shtml 文件路径，不填写则不生成shtml文件"></div>';
                    }
                    content += '</div>';
                    var inputDialog = $(document).dialog({
                        type: "confirm"
                        ,titleText: "上传文件"
                        ,content: content
                        ,contentScroll: false
                        ,onShow: function() {
                            if (suffix=="css" || suffix=="js") {
                                $.ajax({
                                        type: 'get'
                                        ,
                                        dataType: 'json'
                                        ,
                                        url: "/static_file/last_version?project_key=" + project_key + "&source_path=" + params.file_path
                                        ,
                                        data: {dtype: "json"}
                                        ,
                                        success: function (data, textStatus, jqXHR) {
                                            if (data.code == 0 || data.code == 3) {
                                                if (data.code == 0) {
                                                    $("#shtml_path").val(data.data.info.sinclude_path);
                                                    $("#rename_file").attr("checked", data.data.info.rename==1);
                                                    $("#dev_model").attr("checked", data.data.info.dev_model==1);
                                                }
                                            } else {
                                                $(document).dialog({
                                                    type: "notice"
                                                    , position: "bottom"
                                                    , dialogClass: "dialog_warn"
                                                    , infoText: data.msg
                                                    , autoClose: 3000
                                                    , overlayShow: false
                                                });
                                            }
                                        }
                                        ,
                                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                                            $(document).dialog({
                                                type: "notice"
                                                , position: "bottom"
                                                , dialogClass: "dialog_red"
                                                , infoText: textStatus
                                                , autoClose: 3000
                                                , overlayShow: false
                                            });
                                        }
                                    }
                                );
                            }
                        }
                        ,onClickConfirmBtn: function(){
                            if (suffix=="css" || suffix=="js"){
                                params.shtml_path = $.trim($("#shtml_path").val());
                                if(params.shtml_path != "" && "shtml"!==params.shtml_path.substring(params.shtml_path.lastIndexOf(".")+1).toLowerCase()){
                                    $(document).dialog({
                                        type: "notice"
                                        , position: "bottom"
                                        , dialogClass: "dialog_warn"
                                        , infoText: "shtml文件名后缀错误"
                                        , autoClose: 3000
                                        , overlayShow: false
                                    });
                                    return false;
                                }
                            }
                            params.rename = $("#rename_file").prop("checked")?1:0;
                            params.dev_model = $("#dev_model").prop("checked")?1:0;
                            var toast = loading();
                            $.ajax({
                                    type: 'post'
                                    , dataType: 'json'
                                    , url: url
                                    , data: params
                                    , success: function (data, textStatus, jqXHR) {
                                        toast.close();
                                        if (data.code == 0) {
                                            inputDialog.close();
                                            content = '<div class="text-left"><div>本地文件：'+data.data.local_path+'</div>' +
                                                '<div>上传后的文件：'+data.data.visit_path+'</div>';
                                            if (suffix=="css" || suffix=="js") {
                                                if (data.data.shtml_path.length>0) {
                                                    content += '<div>引用代码：&lt;!--#include virtual="' + data.data.shtml_path + '"--&gt;</div>';
                                                }
                                            }
                                            content += '</div>';
                                            $(document).dialog({
                                                titleText: "上传完成"
                                                ,content: content
                                                ,contentScroll: false
                                            });
                                        }
                                        else {
                                            $(document).dialog({
                                                type: "notice"
                                                , position: "bottom"
                                                , dialogClass: "dialog_warn"
                                                , infoText: data.msg
                                                , autoClose: 3000
                                                , overlayShow: false
                                            });
                                        }
                                    }
                                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                                        toast.close();
                                        $(document).dialog({
                                            type: "notice"
                                            ,position: "bottom"
                                            ,dialogClass:"dialog_red"
                                            ,infoText: textStatus
                                            ,autoClose: 3000
                                            ,overlayShow: false
                                        });
                                    }
                                }
                            );
                            return false;
                        }
                    });
                }
            }
        }
    });
</script>