<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 16:37
 */

namespace app\common\validate;

use think\Validate;

class FlowGroupValidate extends Validate
{
    protected $rule = [
        'city_id'           => 'require',
        'agency_id'         => 'require',
        'name'              => 'require',
        'isvalid'           => 'require',
    ];

    protected $message = [
        'city_id.require'   => '请选择城市',
        'agency_id.require' => '请选择公司',
        'name.require'      => '请输入组名称',
        'isvalid.require'   => '请确认是否有效',
    ];

    protected $scene = [
        'create'         => ['city_id', 'agency_id', 'name', 'isvalid'],
        'edit'           => ['id', 'city_id', 'agency_id', 'name'],
    ];
}