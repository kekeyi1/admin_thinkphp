<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 9:00
 */

namespace app\common\validate;

use think\Validate;

class ClearingValidate extends Validate
{
    protected $rule = [
        'service_charge'   => 'require|float',
        'invoice_price'    => 'require|float', 
        'invoice_title'    => 'require',
    ];

    protected $message = [
        'service_charge.require'  => '请输入服务费',
        'service_charge.float'    => '服务费必须是数字',
        'invoice_price.require'   => '请输入开票金额',
        'invoice_price.float'     => '开票金额必须是数字',
        'invoice_title.require'   => '请输入发票抬头',
    ];

    protected $scene = [
        'create' => ['service_charge', 'invoice_price', 'invoice_title'],
    ];
}