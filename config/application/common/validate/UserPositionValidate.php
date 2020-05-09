<?php
/**
 * User: yangyujuan
 * Date: 2019/12/10
 * Time: 16:00
 */

namespace app\common\validate;

use think\Validate;

class UserPositionValidate extends Validate
{
    protected $rule = [
        'name'    => 'require|max:25',
        'sort'    => 'number',
    ];

    protected $message = [
        'name.require'      => '请输入岗位名称',
        'name.max'          => '岗位名称长度不超过25位',
        'sort.number'       => '排序必须是数字',
    ];

    protected $scene = [
        'create'         => ['name','sort'],
        'edit'           => ['name','sort'],
    ];
}