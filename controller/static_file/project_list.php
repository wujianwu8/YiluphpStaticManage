<?php
/**
 * @name 项目列表页
 * @desc
 * @method GET
 * @uri /static_file/project_list
 * @param integer page 页码 可选 默认为1
 * @param integer page_size 每页条数 可选 默认为10
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

$page = $app->input->get_int('page',1);
$page_size = $app->input->get_int('page_size',10);
$page_size>500 && $page_size = 500;
$page_size<1 && $page_size = 1;

$where = [];
$data_list = $app->model_static_project->paging_select($where, $page, $page_size, 'ctime DESC');
foreach ($data_list as $key=>$item){
    if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_project_'.$item['project_key'])) {
        unset($data_list[$key]);
    }
}
return_result('static_file/project_list', [
    'data_list' => $data_list,
    'data_count' => $app->model_static_project->count($where),
    'page' => $page,
    'page_size' => $page_size,
]);