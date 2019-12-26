<?php
/**
 * @name 获取文件最后一次版本的信息
 * @desc
 * @method GET
 * @uri /static_file/last_version
 * @param string project_key 项目键 必选
 * @param string source_path 文件路径 必选
 * @return json
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_key' => 'required|trim|string|min:1|max:200|return',
        'source_path' => 'required|trim|string|min:1|max:200|return',
    ],
    [],
    [
        'project_key.*' => 1,
        'source_path.*' => 2,
    ]);

if (!$version_info=$app->model_file_version->find_table(
        ['project_key'=>$params['project_key'], 'source_path'=>$params['source_path']],
        "*", null, ' ORDER BY id DESC '
    )){
    return_code(3, '没有找到版本信息');
}

return_code(CODE_SUCCESS, '获取成功', ['info'=>$version_info]);