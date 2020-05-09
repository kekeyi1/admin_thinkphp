<?php
/**
 * User: yangyujuan
 * Date: 2019/12/10
 * Time: 10:33
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterStateValidate extends Validate
{
    protected $rule = [
        'name'    => 'require|max:20',
        'sort'    => 'number',
    ];

    protected $message = [
        'name.require'      => '请输入标的物状态名称',
        'name.max'          => '标的物状态名称长度不超过20位',
        'sort.number'       => '排序必须是数字',
    ];

    protected $scene = [
        'create'         => ['name','sort'],
        'edit'           => ['name','sort'],
    ];
}