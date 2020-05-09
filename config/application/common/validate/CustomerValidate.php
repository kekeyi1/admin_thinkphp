<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/3
 * Time: 15:28
 */

namespace app\common\validate;

use think\Validate;

class CustomerValidate extends Validate
{
    protected $rule = [
        'name'              => 'require|token',
        'phone'             => 'require|mobile',
        'sex'               => 'require|number',
        'state'             => 'require',
        'referees'          => 'require',
        'intention_product' => 'require',
        'create_uid'        => 'require',
    ];

    protected $message = [
        'name.require'              => '请输入客户姓名',
        'phone.require'             => '请输入客户手机',
        'sex.require'               => '请选择性别',
        'state.require'             => '请选择客户状态',
        'referees.require'          => '请选择推荐人',
        'intention_product.require' => '请选择意向产品',
        'create_uid.require'        => '未登录',
        'phone.mobile'              => '手机号格式错误',
    ];

    protected $scene = [
        'create'         => ['name', 'phone', 'sex', 'state', 'referees', 'intention_product', 'create_uid'],
        'edit'           => ['name', 'phone', 'sex', 'state', 'referees', 'intention_product'],
    ];
}