<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/10
 * Time: 9:00
 */

namespace app\common\validate;

use think\Validate;

class RegionValidate extends Validate
{
    protected $rule = [
        'name'   => 'require',
        'pinyin'    => 'alpha',
        'short_pinyin'    => 'alpha',
        'sort'    => 'number',
    ];

    protected $message = [
        'name.require'  => '请输入地区名称',
        'pinyin.alpha'   => '拼音必须是字母',
        'short_pinyin.alpha'    => '拼音首字母必须是字母',
        'sort.number'   => '排序必须是数字',
    ];

    protected $scene = [
        'create'         => ['name', 'pinyin', 'short_pinyin', 'sort'],
        'edit'           => ['name', 'pinyin', 'short_pinyin', 'sort'],
    ];
}