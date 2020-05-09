<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 11:03
 */

namespace app\common\validate;

use think\Validate;

class FlowConfigValidate extends Validate
{
    protected $rule = [
        'workflow_type_id'    => 'require',
        'workflow_version_id' => 'require',
        'workflow_node_id'    => 'require',
        'step'                => 'number|between:0,65535',
        'type'                => 'require',
        'role_id'             => 'require',
        'sql_text'            => 'require',
    ];

    protected $message = [
        'workflow_type_id.require'    => '请选择流程类型',
        'workflow_version_id.require' => '请选择流程版本',
        'workflow_node_id.require'    => '请选择节点',
        'step.require'                => '请输入步骤',
        'type.require'                => '请选择对接类型',
        'role_id.require'             => '请选择对接人/组',
        'sql_text.require'            => '请输入SQL',
    ];

    protected $scene = [
        'create'         => ['workflow_type_id', 'workflow_version_id', 'workflow_node_id', 'step', 'type'],
        'edit'           => ['id', 'workflow_type_id', 'workflow_version_id', 'workflow_node_id', 'step', 'type'],
    ];
}