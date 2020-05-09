<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 22:41
 */

namespace app\common\validate;

use think\Validate;

class FlowNodeValidate extends Validate
{
    protected $rule = [
        'name'              => 'require',
        'create_uid'        => 'require',
        'isvalid'           => 'require',
        'sort'              => 'number|between:0,65535',
    ];

    protected $message = [
        'name.require'              => '请输入流程类型名称',
        'create_uid.require'        => '未登录',
        'isvalid.require'           => '请选择是否有效',
        'sort.require'              => '请正确输入排序',
    ];

    protected $scene = [
        'create'         => ['name', 'create_uid', 'isvalid', 'sort'],
        'edit'           => ['id', 'name', 'create_uid', 'sort'],
    ];
}