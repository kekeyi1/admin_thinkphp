<?php
/**
 * User: yangyujuan
 * Date: 2019/12/16
 * Time: 10:33
 */

namespace app\common\validate;

use think\Validate;

class DeptValidate extends Validate
{
    protected $rule = [
        'dept_name'    => 'require|max:30',
    ];

    protected $message = [
        'dept_name.require'      => '请输入部门名称',
        'dept_name.max'          => '部门名称长度不超过30位',
    ];

    protected $scene = [
        'create'         => ['dept_name'],
        'edit'           => ['dept_name'],
    ];
}