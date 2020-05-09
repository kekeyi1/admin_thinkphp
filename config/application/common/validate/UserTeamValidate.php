<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 9:00
 */

namespace app\common\validate;

use think\Validate;

class UserTeamValidate extends Validate
{
    protected $rule = [
        'name'      => 'require',
        'agency_id' => 'require',
        'manager'   => 'require',
        'leader'    => 'require',
    ];

    protected $message = [
        'name.require'      => '请输入团队名称',
        'agency_id.require' => '请选择法院',
        'manager.require'   => '请选择区域经理',
        'leader.require'    => '请选择主管',
    ];

    protected $scene = [
        'create' => ['name', 'agency_id', 'manager', 'leader'],
        'edit'   => ['name', 'agency_id', 'manager', 'leader'],
    ];
}