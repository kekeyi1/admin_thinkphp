<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 16:37
 */

namespace app\common\validate;

use think\Validate;

class FlowGroupUserValidate extends Validate
{
    protected $rule = [
        'workflow_group_id' => 'require',
        'uid'               => 'require',
        'isvalid'           => 'require',
        'create_uid'        => 'require',
    ];

    protected $message = [
        'workflow_group_id.require'  => '请输选择流程组',
        'uid.require'                => '请选择流程组人员',
        'isvalid.require'            => '请确认是否有效',
        'create_uid.require'         => '未登录',
    ];

    protected $scene = [
        'create'         => ['workflow_group_id', 'uid', 'isvalid', 'create_uid'],
        'edit'           => ['id', 'workflow_group_id', 'uid', 'create_uid'],
    ];
}