<?php
/**
 * @name 上传文件至阿里云
 * @desc
 * @method POST
 * @uri /static_file/upload_file
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
        'file_path' => 'required|trim|string|min:4|max:200|return',
        'rename' => 'required|integer|min:0|max:1|return',
        'dev_model' => 'required|integer|min:0|max:1|return',
        'shtml_path' => 'trim|string|max:200|return',
    ],
    [],
    [
        'project_key.*' => 1,
        'file_path.*' => 2,
        'rename.*' => 3,
        'dev_model.*' => 4,
    ]);

if (!$project_info=$app->model_static_project->find_table(['project_key'=>$params['project_key']])){
    return_code(5, '项目不存在');
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'upload_file_'.$params['project_key'])) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}
if (empty($params['shtml_path'])) {
    $params['shtml_path'] = '';
}
else{
    $params['shtml_path'] = DIRECTORY_SEPARATOR.$params['shtml_path'];
}

$dir_path = $project_info['static_path'].DIRECTORY_SEPARATOR.$params['file_path'];
$shtml_path = $project_info['shtml_path'].DIRECTORY_SEPARATOR.$params['shtml_path'];
if (DIRECTORY_SEPARATOR =='/'){
    $dir_path = str_replace('\\', '/', $dir_path);
    $dir_path = preg_replace('/\/+/', '/', $dir_path);
    $shtml_path = str_replace('\\', '/', $shtml_path);
    $shtml_path = preg_replace('/\/+/', '/', $shtml_path);
    $params['file_path'] = str_replace('\\', '/', $params['file_path']);
    $params['file_path'] = preg_replace('/\/+/', '/', $params['file_path']);
    $params['shtml_path'] = str_replace('\\', '/', $params['shtml_path']);
    $params['shtml_path'] = preg_replace('/\/+/', '/', $params['shtml_path']);
}
else{
    $dir_path = str_replace('/', '\\', $dir_path);
    $dir_path = preg_replace('/\\+/', '\\', $dir_path);
    $shtml_path = str_replace('/', '\\', $shtml_path);
    $shtml_path = preg_replace('/\\+/', '\\', $shtml_path);
    $params['file_path'] = str_replace('/', '\\', $params['file_path']);
    $params['file_path'] = preg_replace('/\\+/', '\\', $params['file_path']);
    $params['shtml_path'] = str_replace('/', '\\', $params['shtml_path']);
    $params['shtml_path'] = preg_replace('/\\+/', '\\', $params['shtml_path']);
}
if (!file_exists($dir_path)){
    return_code(6, '文件不存在：'.$dir_path);
}

if (empty($params['dev_model'])) {
    if (empty($params['rename'])) {
        $remote_path = $params['file_path'];
    } else {
        $pathinfo = pathinfo($params['file_path']);
        $remote_path = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.' . date('YmdHis') . '.' . $pathinfo['extension'];
    }
    $remote_path = str_replace('\\', '/', $remote_path);
    if (substr($remote_path, 0, 1) != '/') {
        $remote_path = '/'.$remote_path;
    }
    $remote_path = $project_info['project_key'].$remote_path;
    $visit_path = $app->tool_oss->upload_file($dir_path, $remote_path);
}
else{
    $visit_path = $params['file_path'];
}

if (!empty($params['shtml_path'])) {
    $pathinfo = pathinfo($params['file_path']);
    if (in_array(strtolower($pathinfo['extension']), ['js','css'])) {
        $path = dirname($shtml_path);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (strtolower($pathinfo['extension'])=='js'){
            $txt = '<script src="'.$visit_path.'" type="text/javascript"></script>';
        }
        else{
            $txt = '<link href="'.$visit_path.'" rel="stylesheet">';
        }
        $code = mb_detect_encoding($txt);
        if ($code != 'UTF-8') {
            $txt = iconv($code, 'UTF-8', $txt);
        }
//        if (file_exists($shtml_path)) {
//            chmod($shtml_path, 0777);
//        }
        file_put_contents($shtml_path, $txt);
//        chmod($shtml_path, 0777);
//        $error = error_get_last();
        $shtml_path = DIRECTORY_SEPARATOR . 'include' . $params['shtml_path'];
    }
    else{
        $shtml_path = '';
    }
}
else{
    $shtml_path = '';
}

if (substr($params['shtml_path'], 0, 1) == DIRECTORY_SEPARATOR) {
    $params['shtml_path'] = substr($params['shtml_path'], 1);
}
//版本存入数据库
$data = [
    'uid' => $self_info['uid'],
    'dev_model' => $params['dev_model'],
    'rename' => $params['rename'],
    'project_key' => $params['project_key'],
    'source_path' => $params['file_path'],
    'version_path' => $visit_path,
    'sinclude_path' => $params['shtml_path'],
    'ctime' => time(),
];
$app->model_file_version->insert_table($data);

$data = [
    'local_path' => $dir_path,
    'visit_path' => $visit_path,
    'shtml_path' => $shtml_path,
];
return_code(CODE_SUCCESS, "上传完成", $data);