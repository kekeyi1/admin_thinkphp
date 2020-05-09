<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 9:00
 */

namespace app\common\validate;

use think\Validate;

class AgencyValidate extends Validate
{
    protected $rule = [
        'name'        => 'require',
        'province_id' => 'require',
        'city_id'     => 'require',
        'area_id'     => 'require',
        'address'     => 'require',
    ];

    protected $message = [
        'name.require'        => '请输入公司名称',
        'province_id.require' => '请选择所在地区省份',
        'city_id.require'     => '请选择所在地区城市',
        'area_id.require'     => '请选择所在地区区属',
        'address.require'     => '请输入公司详细地址',
    ];

    protected $scene = [
        'create' => ['name', 'province_id', 'city_id', 'area_id', 'address'],
        'edit'   => ['name', 'province_id', 'city_id', 'area_id', 'address'],
    ];
}