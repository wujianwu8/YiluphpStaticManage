<?php
/**
 * @name 删除项目信息
 * @desc 会保留文件日志
 * @method POST
 * @uri /static_file/delete_project
 * @param integer project_id 项目ID 必选
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'delete_project')) {
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

if (!$project_info=$app->model_static_project->find_table(['id'=>$params['project_id']], 'project_key')){
    return_code(2, '项目不存在');
}
$result = $app->model_user_center->delete_permission_by_key('view_project_'.$project_info['project_key']);
if (empty($result)){
    unset($params,$matches);
    return_code(10, '删除访问静态文件的权限失败。');
}
if ($result['code']>0){
    unset($params,$matches);
    return_code(11, '删除访问静态文件的权限失败。'.$result['msg']);
}
$result = $app->model_user_center->delete_permission_by_key('upload_file_'.$project_info['project_key']);
if (empty($result)){
    unset($params,$matches);
    return_code(10, '删除上传静态文件的权限失败。');
}
if ($result['code']>0){
    unset($params,$matches);
    return_code(11, '删除上传静态文件的权限失败。'.$result['msg']);
}

$app->model_static_project->delete(['id'=>$params['project_id']]);

//返回结果
return_json(CODE_SUCCESS,'删除成功');