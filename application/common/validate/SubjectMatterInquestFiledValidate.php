<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/9
 * Time: 15:35
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterInquestFiledValidate extends Validate
{
    protected $rule = [
        'name'           => 'require|max:8|token',
        'field'          => 'require',
        'field_type'     => 'require',
        'field_format'   => 'require',
        'field_length'   => 'require|number|between:0,120',
//        'default_value'  => 'require',
        'is_required'    => 'require|between:0,1',
        'create_uid'     => 'require',
        'widget_type'    => 'require',
        'isvalid'        => 'require',
        'is_inline'      => 'require',
    ];

    protected $message = [
        'name.require'           => '请输入字段名称',
        'field.require'          => '请输入字段英文',
        'field_type.require'     => '请选择字段类型',
        'field_format.require'   => '请选择字段格式',
        'field_length.require'   => '请输入字段长度',
        'widget_type.require'    => '请选择控件类型',
//        'default_value.require'  => '请输入默认值',
        'is_required.require'    => '请选择是否必填',
        'create_uid.require'     => '未登录',
        'isvalid.require'        => '请选择是有效',
        'is_inline.require'      => '请选择是is_inline',
    ];

    protected $scene = [
        'create' => ['name', 'create_uid', 'field', 'field_type', 'field_format', 'widget_type', 'field_length', 'is_required', 'isvalid', 'is_inline'],
        'edit' => ['id', 'name', 'create_uid', 'field', 'field_format', 'field_length',  'is_required'],
    ];
}