<!--{use_layout layout/main}-->
<?php
$head_info = [
    'title' => '项目列表',
];
?>

<div class="row mb-3">
    <a href="/static_file/add_project" class="btn btn-sm btn-outline-primary ml-4 ajax_main_content">
        <i class="fa fa-plus" aria-hidden="true"></i>
        <?php echo $app->lang('添加项目'); ?>
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm table_list" id="data_list">
        <thead>
        <tr>
            <th><?php echo $app->lang('项目键名'); ?></th>
            <th><?php echo $app->lang('项目名称'); ?></th>
            <th><?php echo $app->lang('描述'); ?></th>
            <th><?php echo $app->lang('文件目录'); ?></th>
            <th><?php echo $app->lang('shtml目录'); ?></th>
            <th><?php echo $app->lang('create_time'); ?></th>
            <th><?php echo $app->lang('operation'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data_list as $item): ?>
        <tr _id="<?php echo $item['id']; ?>">
            <td><?php echo $item['project_key']; ?></td>
            <td>
                <a class="mr-1" href="/static_file/file_list?project_key=<?php echo $item['project_key']; ?>">
                    <?php echo $item['project_name']; ?>
                </a>
            </td>
            <td><?php echo $item['description']; ?></td>
            <td><?php echo $item['static_path']; ?></td>
            <td><?php echo $item['shtml_path']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $item['ctime']); ?></td>
            <td>
                <a class="mr-2" href="/static_file/file_list?project_key=<?php echo $item['project_key']; ?>">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                </a>
                <a class="mr-1" href="/static_file/edit_project/<?php echo $item['id']; ?>">
                    <i class="fa fa-edit" aria-hidden="true"></i>
                </a>
                <a href="/static_file/delete_project" class="delete"><i class="fa fa-close"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>

        <?php if(empty($data_list)): ?>
            <tr>
                <td colspan="7" class="pt-5 pb-5"><center><?php echo $app->lang('no_data'); ?></center></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php echo $app->pager->display_pages([
    'data_count' => $data_count,
    'page' => $page,
    'page_size' => $page_size,
//    'a_class' => 'ajax_main_content',
    'first_page_text' => $app->lang('first_page'),
    'pre_page_text' => $app->lang('previous_page'),
    'next_page_text' => $app->lang('next_page'),
    'last_page_text' => $app->lang('last_page'),
]); ?>

<script>

    $("#data_list").click(function(e){
        var obj = null;
        if(e.target.tagName.toLocaleUpperCase() == "A"){
            obj = $(e.target);
        }
        else if(e.target.parentNode.tagName.toLocaleUpperCase() == "A"){
            obj = $(e.target.parentNode);
        }

        if(obj!==null) {
            if (obj.hasClass("delete")) {
                e.preventDefault();
                project_id = obj.parents("tr").attr("_id");
                url = obj.attr("href");
                $(document).dialog({
                    type : 'confirm',
                    closeBtnShow: true,
                    titleText: getLang("notice"),
                    buttonTextConfirm: getLang("delete_now"),
                    buttonTextCancel: getLang("cancel"),
                    content: "你确定要删除项目："+$(obj.parents("tr").find("td")[1]).text()+" 吗？删除后不可恢复",
                    onClickConfirmBtn: function(){
                        var params = {
                            dtype:"json"
                            ,project_id: project_id
                        };
                        var toast = loading();
                        $.ajax({
                            type: 'post'
                            , dataType: 'json'
                            , url: url
                            , data: params
                            , success: function (data, textStatus, jqXHR) {
                                toast.close();
                                if (data.code == 0) {
                                    $(document).dialog({
                                        type: "notice"
                                        , position: "bottom"
                                        , infoText: data.msg
                                        , autoClose: 2000
                                        , overlayShow: false
                                    });
                                    obj.parents("tr").remove();
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
        }
    });
</script>