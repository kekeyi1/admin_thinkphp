<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 18:05
 */

namespace app\common\validate;

use think\Validate;

class FlowVersionValidate extends Validate
{
    protected $rule = [
        'workflow_type_id'  => 'require|token',
        'name'              => 'require',
        'create_uid'        => 'require',
        'isvalid'           => 'require',
    ];

    protected $message = [
        'workflow_type_id.require'  => '请选择业务类型',
        'name.require'              => '请输入流程类型名称',
        'create_uid.require'        => '未登录',
        'isvalid.require'          => '请选择是否有效',
    ];

    protected $scene = [
        'create'         => ['workflow_type_id', 'name', 'create_uid', 'isvalid'],
        'edit'           => ['id', 'workflow_type_id', 'name', 'create_uid', 'isvalid'],
    ];
}