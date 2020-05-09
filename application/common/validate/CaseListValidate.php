<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 10:39
 */

namespace app\common\validate;

use think\Validate;

class CaseListValidate extends Validate
{
    protected $rule = [
        'court_id'   => 'require|token',
        'judge_uid'  => 'require',
        'case_number'=> 'require',
        'create_uid' => 'require',
        'team_id'    => 'require',
        'state'      => 'require',
        'desr'       => 'max:120',
    ];

    protected $message = [
        'court_id.require'   => '请选择法院',
        'judge_uid.require'  => '请选择承办法官',
        'case_number.require'=> '请输入执行编号',
        'create_uid.require' => '未登录',
        'team_id.require'    => '请选择团队',
        'state.require'      => '请确认案件状态',
        'desr'               => '描述内容120字以内',
    ];

    protected $scene = [
        'create'         => ['court_id', 'judge_uid', 'case_number', 'create_uid', 'desr', 'state', 'team_id'],
        'edit'           => ['id', 'court_id', 'judge_uid', 'case_number', 'desr'],
    ];
}