<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 16:48
 */

namespace app\common\validate;

use think\Validate;

class FlowTypeValidate extends Validate
{
    protected $rule = [
        'type_id'    => 'require|token',
        'name'       => 'require',
        'create_uid' => 'require',
        'isvalid'    => 'require',
        'sort'        => 'number|between:0,65535',
    ];

    protected $message = [
        'type_id.require'    => '请选择业务类型',
        'name.require'       => '请输入流程类型名称',
        'create_uid.require' => '未登录',
        'isvalid.require'    => '请选择是否有效',
        'sort'               => '请正确输入排序',
    ];

    protected $scene = [
        'create'         => ['type_id', 'name', 'create_uid', 'isvalid', 'sort'],
        'edit'           => ['id', 'type_id', 'name', 'create_uid', 'sort'],
    ];
}