<?php
/**
 * @name 项目下的文件列表
 * @desc
 * @method GET
 * @uri /static_file/file_list
 * @param string project_key 项目键 必选
 * @param string from_dir 当前目录 可选 不传此参数则从static目录开始
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_key' => 'required|trim|string|min:1|max:200|return',
        'from_dir' => 'trim|string|return',
    ],
    [],
    [
        'project_key.*' => 1,
        'from_dir.*' => 2,
    ]);

if (!$project_info=$app->model_static_project->find_table(['project_key'=>$params['project_key']])){
    return_code(3, '项目不存在');
}

if (!isset($params['from_dir']) || $params['from_dir']==''){
    $params['from_dir'] = '';
}
$params['from_dir'] .= '/';
$dir_path = $project_info['static_path'].DIRECTORY_SEPARATOR.$params['from_dir'];
if (DIRECTORY_SEPARATOR =='/'){
    $dir_path = str_replace('\\', '/', $dir_path);
    $dir_path = preg_replace('/\/+/', '/', $dir_path);
    $params['from_dir'] = str_replace('\\', '/', $params['from_dir']);
    $params['from_dir'] = preg_replace('/\/+/', '/', $params['from_dir']);
}
else{
    $dir_path = str_replace('/', '\\', $dir_path);
    $dir_path = preg_replace('/\\+/', '\\', $dir_path);
    $params['from_dir'] = str_replace('/', '\\', $params['from_dir']);
    $params['from_dir'] = preg_replace('/\\+/', '\\', $params['from_dir']);
}
if (!is_dir($dir_path)){
    return_code(4, '目录不存在：'.$dir_path);
}

$file_list = get_dir_and_file($dir_path);
$dir_arr = explode(DIRECTORY_SEPARATOR, $params['from_dir']);
$dir_arr = array_filter($dir_arr);

return_result('static_file/file_list',[
    'project_info' => $project_info,
    'from_dir' => $params['from_dir'],
    'dir_arr' => $dir_arr,
    'file_list' => $file_list,
]);