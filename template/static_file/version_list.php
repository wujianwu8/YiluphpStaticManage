<!--{use_layout layout/main}-->
<?php
$head_info = [
    'title' => '文件上传记录',
];
?>

<div class="mb-2">
    <a class="mr-3" href="javascript:window.history.back();">返回</a>
    <?php if (!empty($project_info)): ?>
        <span class="mr-3">项目：<strong class="text-danger"><?php echo $project_info['project_name']; ?></strong></span>
    <?php endif; ?>
    <?php if (!empty($source_path)): ?>
        <span>文件/目录：<strong class="text-danger"><?php echo $source_path; ?></strong></span>
    <?php endif; ?>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm table_list" id="data_list">
        <thead>
        <tr>
        <?php if (empty($project_info)): ?>
            <th><?php echo $app->lang('项目名称'); ?></th>
        <?php endif; ?>
        <?php if (empty($source_path)): ?>
            <th><?php echo $app->lang('源文件'); ?></th>
        <?php endif; ?>
            <th><?php echo $app->lang('访问URL'); ?></th>
            <th><?php echo $app->lang('上传人'); ?></th>
            <th><?php echo $app->lang('上传时间'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data_list as $item): ?>
        <tr>
            <?php if (empty($project_info)): ?>
            <td><?php echo $item['project_name']; ?></td>
            <?php endif; ?>
            <?php if (empty($source_path)): ?>
            <td><?php echo $item['source_path']; ?></td>
            <?php endif; ?>
            <td><?php echo $item['version_path']; ?></td>
            <td><a href="<?php echo $config['user_center']['host']; ?>/user/detail/<?php echo $item['uid']; ?>"><?php echo $item['nickname']; ?></a></td>
            <td><?php echo date('Y-m-d H:i:s', $item['ctime']); ?></td>
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
