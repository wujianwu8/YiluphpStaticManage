<?php
/**
 * @name 上传目录下的文件至阿里云
 * @desc
 * @method POST
 * @uri /static_file/upload_folder
 * @param string project_key 项目键 必选
 * @param string dir_path 目录路径 必选
 * @return json
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_key' => 'required|trim|string|min:1|max:200|return',
        'dir_path' => 'required|trim|string|min:4|max:200|return',
    ],
    [],
    [
        'project_key.*' => 1,
        'dir_path.*' => 2,
        'rename.*' => 3,
        'dev_model.*' => 4,
    ]);

if (!$project_info=$app->model_static_project->find_table(['project_key'=>$params['project_key']])){
    return_code(5, '项目不存在');
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'upload_file_'.$params['project_key'])) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$dir_path = $project_info['static_path'].DIRECTORY_SEPARATOR.$params['dir_path'];
if (DIRECTORY_SEPARATOR =='/'){
    $dir_path = str_replace('\\', '/', $dir_path);
    $dir_path = preg_replace('/\/+/', '/', $dir_path);
    $params['dir_path'] = str_replace('\\', '/', $params['dir_path']);
    $params['dir_path'] = preg_replace('/\/+/', '/', $params['dir_path']);
}
else{
    $dir_path = str_replace('/', '\\', $dir_path);
    $dir_path = preg_replace('/\\+/', '\\', $dir_path);
    $params['dir_path'] = str_replace('/', '\\', $params['dir_path']);
    $params['dir_path'] = preg_replace('/\\+/', '\\', $params['dir_path']);
}
if (substr($dir_path,-1)=='\\' || substr($dir_path,-1)=='/'){
    $dir_path = substr($dir_path, 0, strlen($dir_path)-1);
}
if (!is_dir($dir_path)){
    return_code(6, '目录不存在：'.$dir_path);
}

$file_list = get_dir_and_file($dir_path, 'file');
$succ_file = $error_file = [];
foreach ($file_list as $filename){
    $remote_path = $project_info['project_key'].'/'.$params['dir_path'].'/'.$filename;
    $remote_path = str_replace('\\', '/', $remote_path);
    $remote_path = preg_replace('/\/+/', '/', $remote_path);
    if ($visit_path = $app->tool_oss->upload_file($dir_path.DIRECTORY_SEPARATOR.$filename, $remote_path)){
        $succ_file[] = $visit_path;
    }
    else{
        $error_file[] = $params['dir_path'].DIRECTORY_SEPARATOR.$filename;
    }
}

if ($succ_file) {
    if (substr($params['dir_path'],-1)!='\\' && substr($params['dir_path'],-1)!='/'){
        $params['dir_path'] .= DIRECTORY_SEPARATOR;
    }
    //版本存入数据库
    $data = [
        'uid' => $self_info['uid'],
        'dev_model' => 0,
        'rename' => 0,
        'project_key' => $params['project_key'],
        'source_path' => $params['dir_path'],
        'version_path' => 'All files',
        'sinclude_path' => '',
        'ctime' => time(),
    ];
    $app->model_file_version->insert_table($data);
}

$data = [
    'succ_file' => $succ_file,
    'error_file' => $error_file,
];
return_code(CODE_SUCCESS, "上传完成", $data);