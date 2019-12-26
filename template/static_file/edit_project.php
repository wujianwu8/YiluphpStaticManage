<!--{use_layout layout/main}-->
<?php
$head_info = [
    'title' => $app->lang('修改项目信息'),
];
?>

<h4 class="mb-3"><?php echo $head_info['title']; ?></h4>
<form class="needs-validation title_content" novalidate="" method="post">
    <input type="hidden" name="id" value="<?php echo $project_info['id']; ?>">
    <div class="row mb-2">
        <div class="col-sm-3 title">
            <?php echo $app->lang('项目键名'); ?>
        </div>
        <div class="col-sm-9">
            <?php echo $project_info['project_key']; ?>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-3 title">
            <label for="project_name"><?php echo $app->lang('项目名称'); ?></label>
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="project_name" name="project_name"
                   value="<?php echo $project_info['project_name']; ?>" required="" maxlength="20">
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-3 title">
            <label for="static_path"><?php echo $app->lang('静态资源目录'); ?></label>
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="static_path" name="static_path"
                   value="<?php echo $project_info['static_path']; ?>"
                   placeholder="<?php echo $app->lang('存放静态文件的目录路径'); ?>" required="" maxlength="500">
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-3 title">
            <label for="shtml_path"><?php echo $app->lang('shtml文件目录'); ?></label>
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="shtml_path" name="shtml_path"
                   value="<?php echo $project_info['shtml_path']; ?>"
                   placeholder="<?php echo $app->lang('则此文件作为中间桥梁引入文件'); ?>" required="" maxlength="500">
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-3 title">
            <label for="description"><?php echo $app->lang('描述'); ?></label>
        </div>
        <div class="col-sm-9">
            <textarea class="form-control" id="description" name="description" maxlength="200"><?php echo $project_info['description']; ?></textarea>
        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary btn-lg btn-block" type="submit"><?php echo $app->lang('提交'); ?></button>
</form>
<div class="mb-5"></div>
<script>
    (function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');

        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if (form.checkValidity() === false) {
                    form.classList.add('was-validated');
                    return false;
                }

                var params = {
                    dtype:"json"
                };
                var inputs = $(form).serializeArray();
                for(var index in inputs){
                    var item = inputs[index];
                    params[item.name] = item.value;
                }
                ajaxPost("/static_file/save_edit_project", params, function (data) {
                    $(document).dialog({
                        overlayClose: true
                        , titleShow: false
                        , content: getLang("save_successfully")
                        , onClosed: function() {
                            document.location.href = "/static_file/project_list";
                        }
                    });
                });
            }, false);
        });
    })();
</script>