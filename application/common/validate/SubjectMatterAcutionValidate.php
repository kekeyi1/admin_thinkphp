<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 10:39
 */

namespace app\common\validate;

use think\Validate;

class SubjectMatterAcutionValidate extends Validate
{
    protected $rule = [
        'arsis_address'      => 'require',
        'acution_number'     => 'require',
        'hanging_net_time'   => 'require',
        'auction_start_time' => 'require',
        'auction_end_time'   => 'require',
        'starting_price'     => 'float',
        'reserve_price'      => 'float',
        'deposit'            => 'float',
        'change_price'       => 'float',
    ];

    protected $message = [
        'arsis_address.require'      => '上拍地址不能为空',
        'acution_number.require'     => '请选择拍卖次数',
        'hanging_net_time.require'   => '请选择挂网时间',
        'starting_price.float'       => '起拍价必须是数字',
        'reserve_price.float'        => '保留价必须是数字',
        'deposit.float'              => '保证金必须是数字',
        'change_price.float'         => '拍卖增加幅度必须是数字',
        'auction_start_time.require' => '请选择拍卖开始时间',
        'auction_end_time.require'   => '请选择拍卖结束时间',
    ];

    protected $scene = [
        'create'         => ['arsis_address', 'acution_number', 'hanging_net_time', 'auction_start_time', 'auction_end_time', 'starting_price', 'reserve_price', 'deposit', 'change_price'],
        'edit'           => ['acution_number', 'hanging_net_time', 'auction_start_time', 'auction_end_time', 'starting_price', 'reserve_price', 'deposit', 'change_price'],
    ];
}