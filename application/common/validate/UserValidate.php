<?php
namespace app\common\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'username'   => 'require',
        'ids'        => 'require',
        'agency_id'  => 'require',
        'dept_id'    => 'require',
        'origin_password'   => 'require|min:6',
        'password'   => 'require|min:6',
        'repassword' => 'require|confirm:password',
        'true_name'  => 'require',
        'phone'      => 'require|mobile',
        'position_id'=> 'require',
        'origin_password'=> 'require',
        'email'      => 'email',
    ];

    protected $message = [
        'username.require'   => '请输入用户名',
        'ids.require'        => '请勾选归属角色',
        'agency_id.require'  => '请选择公司',
        'dept_id.require'    => '请选择部门',
        'origin_password.require'   => '请输入原始密码',
        'password.require'   => '请输入密码',
        'password.min'       => '密码不能小于6个字符',
        'repassword.require' => '请再次输入密码',
        'repassword.confirm' => '两次密码输入不一致',
        'true_name.require'  => '请输入姓名',
        'phone.require'      => '请输入手机号',
        'phone.mobile'       => '手机号格式错误',
        'position_id.require'=> '请选择岗位',
        'email.email'        => '电子邮件格式错误',
    ];
    
    protected $scene = [
        'create'         => ['username', 'ids', 'agency_id', 'dept_id', 'password', 'repassword', 'true_name', 'phone', 'email', 'position_id'],
        'edit'           => ['ids', 'agency_id',  'dept_id', 'true_name', 'phone', 'email', 'position_id'],
        'updatePassword' => ['origin_password', 'password', 'repassword'],
        'login'          => ['username', 'password'],
        'register'       => ['username', 'password'],
        'forget'         => ['phone', 'code', 'password', 'repassword'],
        'editPassport'   => ['origin_password', 'password', 'repassword'],
    ];
}