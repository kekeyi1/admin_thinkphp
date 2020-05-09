<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 16:28
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterBaseValidate extends Validate
{
    protected $rule = [
        'name'                  => 'require|max:45',
        'province_id'           => 'require',
        'city_id'               => 'require',
        'area_id'               => 'require',
        'auction_state'         => 'require',
        'create_uid'            => 'require',
        'subject_matter_id'     => 'require',
        'id'                    => 'require',
    ];

    protected $message = [
        'name.require'                 => '请输入标的名称',
        'province_id.require'          => '请选择省份',
        'city_id.require'              => '请选择城市',
        'area_id.require'              => '请选择地区',
        'auction_state'                => '请选择拍卖状态',
        'create_uid'                   => '未登录',
        'id.require'                   => '参数错误',
        'subject_matter_id.require'    => '参数错误',
    ];

    protected $scene = [
        'edit'           => ['id', 'subject_matter_id', 'name', 'province_id', 'city_id', 'area_id', 'auction_state', 'create_uid',],
    ];
}