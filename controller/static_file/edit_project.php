<?php
/**
 * @name 编辑项目信息页
 * @desc
 * @method GET
 * @uri /static_file/edit_project/{project_id}
 * @param integer project_id 项目ID 必选 嵌入在URL中
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'edit_project')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_id' => 'required|integer|min:1|return',
    ],
    [
        'project_id.*' => '项目ID参数错误',
    ],
    [
        'project_id.*' => 1,
    ]);

if (!$project_info=$app->model_static_project->find_table(['id'=>$params['project_id']])){
    return_code(2, '项目不存在');
}

return_result('static_file/edit_project',[
    'project_info' => $project_info
]);