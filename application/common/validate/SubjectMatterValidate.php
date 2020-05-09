<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 16:28
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterValidate extends Validate
{
    protected $rule = [
        'name'                  => 'require|max:45|token',
        'property_type_first'   => 'require',
        'property_type_second'  => 'require',
        'province_id'           => 'require',
        'city_id'               => 'require',
        'area_id'               => 'require',
        'auction_state'         => 'require',
        'case_id'               => 'require',
        'create_uid'            => 'require',
        'subject_matter_number' => 'require',
    ];

    protected $message = [
        'name.require'                 => '请输入标的物名称',
        'property_type_first.require'  => '请选择财产类型',
        'property_type_second.require' => '请选择财产类型',
        'province_id.require'          => '请选择省份',
        'city_id.require'              => '请选择城市',
        'area_id.require'              => '请选择地区',
        'auction_state'                => '请选择拍卖状态',
        'case_id'                      => '未关联案件',
        'create_uid'                   => '未登录',
        'subject_matter_number'        => '未生成标的物编号',
    ];

    protected $scene = [
        'create'         => ['name', 'property_type_first', 'property_type_second', 'province_id', 'city_id', 'area_id', 'auction_state', 'case_id', 'create_uid', 'subject_matter_number'],
        'edit'           => ['id', 'name', 'property_type_first', 'property_type_second', 'province_id', 'city_id', 'area_id', 'auction_state'],
    ];
}