<?php
/**
 * @name 添加项目页
 * @desc
 * @method GET
 * @uri /static_file/add_project
 * @return HTML
 */

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'view_static_manage')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

if (!$app->model_user_center->check_user_permission($self_info['uid'], 'add_project')) {
    return_code(CODE_NO_AUTHORIZED, $app->lang('not_authorized'));
}

return_result('static_file/add_project');