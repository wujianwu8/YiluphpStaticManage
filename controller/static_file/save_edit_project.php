<?php
/**
 * @name 保存修改后的项目信息
 * @desc
 * @method POST
 * @uri /static_file/save_edit_project
 * @param integer id 项目ID 必选 项目ID
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

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'edit_project')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'id' => 'required|integer|min:1|return',
        'project_name' => 'required|trim|string|min:1|max:20|return',
        'static_path' => 'required|trim|string|min:2|max:500|return',
        'shtml_path' => 'required|trim|string|min:2|max:500|return',
        'description' => 'trim|string|max:200|return',
    ],
    [],
    [
        'id.*' => 7,
        'project_name.*' => 3,
        'static_path.*' => 4,
        'shtml_path.*' => 5,
        'description.*' => 6,
    ]);


if (!$app->model_static_project->find_table(['id' => $params['id']])){
    unset($params,$matches);
    return_code(9,'项目不存在');
}

$where = ['id' => $params['id']];
unset($params['id']);
//保存入库
if(false === $app->model_static_project->update_table($where, $params)){
    unset($params,$matches,$where);
    return_code(1, '保存失败');
}

unset($params, $where);
//返回结果
return_json(0,'保存成功');
