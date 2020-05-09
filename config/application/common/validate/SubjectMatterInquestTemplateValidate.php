<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/9
 * Time: 15:05
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterInquestTemplateValidate extends Validate
{
    protected $rule = [
        'property_type_first'   => 'require|between:1,255',
        'name'                  => 'require|max:60|token',
        'create_uid'            => 'require',
        'sort'                  => 'require|between:0,65535',
    ];

    protected $message = [
        'property_type_first.require'  => '请选择财产类型分类',
        'name.require'                 => '请输入模板名称',
        'create_uid.require'           => '未登录',
        'sort.require'                 => '请输入排序',
    ];

    protected $scene = [
        'create'         => ['property_type_first', 'name', 'create_uid', 'sort'],
        'edit'           => ['property_type_first', 'id','name', 'create_uid', 'sort'],
    ];
}