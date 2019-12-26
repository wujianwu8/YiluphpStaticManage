<?php
/**
 * @name 保存新的项目
 * @desc
 * @method POST
 * @uri /static_file/save_add_project
 * @param string project_key 项目键名 必选 项目键名，仅由字母、数字、下划线组成
 * @param string project_name 项目名 必选 可以是语言键名
 * @param string file_dir PHP语言包目录 必选
 * @param string js_file_dir JS语言包目录 必选
 * @param string language_types 语言种类 必选 语言种类只能使用字母、数字、下划线、中横线，半角逗号，长度在2-200个字，多个语种使用半角逗号分隔，如：zh,en
 * @param string description 描述 可选
 * @return json
 * {
 *      code: 0
 *      ,data: []
 *      ,msg: "保存成功"
 * }
 * @exception
 *  0 保存成功
 *  1 保存失败
 *  2 项目键名参数有误
 *  3 项目名参数有误
 *  4 PHP语言包目录参数有误
 *  5 语言各类参数有误
 *  6 描述太长了
 *  7 项目键名只能使用字母、数字、下划线，长度在3-30个字
 *  8 语言种类设置错误，语言种类只能使用字母、数字、下划线、中横线，半角逗号，长度在2-200个字，多个语种使用半角逗号分隔，如：zh,en
 *  9 项目键名已经存在，换一个吧
 * 10 JS语言包目录参数有误
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'add_project')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_key' => 'required|trim|string|min:2|max:20|return',
        'project_name' => 'required|trim|string|min:1|max:20|return',
        'static_path' => 'required|trim|string|min:2|max:500|return',
        'shtml_path' => 'required|trim|string|min:2|max:500|return',
        'description' => 'trim|string|max:200|return',
    ],
    [],
    [
        'project_key.*' => 2,
        'project_name.*' => 3,
        'static_path.*' => 4,
        'shtml_path.*' => 5,
        'description.*' => 6,
    ]);

if (preg_match('/^[a-zA-Z0-9_]{2,20}$/', $params['project_key'], $matches)==false){
    unset($params,$matches);
    return_code(7,'项目键名只能使用字母、数字、下划线，长度在2-20个字');
}

if ($app->model_static_project->find_table(['project_key' => $params['project_key']])){
    unset($params,$matches);
    return_code(9,'项目键名已经存在，换一个吧');
}

$params['ctime'] = time();
//保存入库
if(false === $role_id=$app->model_static_project->insert_table($params)){
    unset($params,$matches);
    return_code(1, '保存失败');
}

//添加相关权限
$result = $app->model_user_center->insert_permission($self_info['uid'],'view_project_'.$params['project_key'],
    '访问静态文件：'.$params['project_name']);
if (empty($result)){
    unset($params,$matches);
    return_code(10, '添加访问静态文件的权限失败。');
}
if ($result['code']>0){
    unset($params,$matches);
    return_code(11, '添加访问静态文件的权限失败。'.$result['msg']);
}
$result = $app->model_user_center->insert_permission($self_info['uid'],'upload_file_'.$params['project_key'],
    '上传静态文件：'.$params['project_name']);
if (empty($result)){
    unset($params,$matches);
    return_code(12, '添加上传静态文件的权限失败。');
}
if ($result['code']>0){
    unset($params,$matches);
    return_code(13, '添加上传静态文件的权限失败。'.$result['msg']);
}

unset($params,$matches);
//返回结果
return_json(CODE_SUCCESS,'保存成功');
