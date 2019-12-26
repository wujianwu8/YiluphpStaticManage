<?php
/**
 * @name 查看项目下某文件的历史记录
 * @desc
 * @method GET
 * @uri /static_file/version_list
 * @param string project_key 项目键 可选
 * @param string source_path 文件/目录路径 可选
 * @param integer page 页码 可选 默认为1
 * @param integer page_size 每页条数 可选 默认为10
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$params = $app->input->validate(
    [
        'project_key' => 'trim|string|max:200|return',
        'source_path' => 'trim|string|max:200|return',
    ],
    [],
    [
        'project_key.*' => 1,
        'source_path.*' => 2,
    ]);

$where = [];
$project_info = null;
if (!empty($params['project_key'])) {
    $project_info = $app->model_static_project->find_table(['project_key'=>$params['project_key']]);
    $where['project_key'] = $params['project_key'];
}
if (!empty($params['source_path'])) {
    $params['source_path'] = preg_replace('/\/+/', '/', $params['source_path']);
    $params['source_path'] = preg_replace('/\\+/', '\\', $params['source_path']);
    $where['source_path'] = $params['source_path'];
}
else{
    $params['source_path'] = null;
}

$page = $app->input->get_int('page',1);
$page_size = $app->input->get_int('page_size',50);
$page_size>500 && $page_size = 500;
$page_size<1 && $page_size = 1;

$data_list = $app->model_file_version->paging_select($where, $page, $page_size, 'id DESC');
$user_info = $project_arr = [];
foreach ($data_list as $key=>$item){
    if (isset($user_info[$item['uid']])){
        $data_list[$key]['nickname'] = $user_info[$item['uid']]['nickname'];
    }
    else{
        if ($temp = $app->model_user_center->find_user_info_by_uid($item['uid'])) {
            $user_info[$item['uid']] = $temp;
            $data_list[$key]['nickname'] = $temp['nickname'];
        }
        else{
            $data_list[$key]['nickname'] = '--';
        }
    }

    if (isset($project_arr[$item['project_key']])){
        $data_list[$key]['project_name'] = $project_arr[$item['project_key']]['project_name'];
    }
    else{
        if ($temp = $app->model_static_project->find_table([ 'project_key'=>$item['project_key'] ], 'project_name')) {
            $project_arr[$item['project_key']] = $temp;
            $data_list[$key]['project_name'] = $temp['project_name'];
        }
        else{
            $data_list[$key]['project_name'] = '--';
        }
    }
}
unset($user_info, $project_arr);
return_result('static_file/version_list', [
    'project_info' => $project_info,
    'source_path' => $params['source_path'],
    'data_list' => $data_list,
    'data_count' => $app->model_file_version->count($where),
    'page' => $page,
    'page_size' => $page_size,
]);