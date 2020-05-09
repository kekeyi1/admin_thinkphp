<?php

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Hook;
use think\facade\Cookie;
use think\facade\Request;
use app\common\model\Auth;
use app\common\model\UserLog;
use app\common\controller\Common;
use app\common\validate\UserValidate;

class Passport extends Common
{
    /**
     * 退出登录
     */
    public function logout()
    {
        Auth::logout();
        $this->redirect('admin/passport/login');
    }

    /**
     * 账号登录
     *
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new UserValidate();
            $validateResult = $validate->scene('login')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            // 并查询用户信息
            $userInfo = Db::name('auth_user')
                ->field('uid,username,password,status,role_ids,true_name,agency_id')
                ->where('username', $request['username'])
                ->find();

            // 用户不存在
            if (!$userInfo) {
                return $this->response(201, Lang::get('user does not exist'));
            }

            // 用户被冻结
            if ($userInfo['status'] == 1) {
                return $this->response(201, Lang::get('the user was frozen'));
            }

            // 验证密码 
            if (!password_verify($request['password'], $userInfo['password'])) {
                return $this->response(201, Lang::get('password error'));
            }

            // 并查询用户团队信息
            $teamInfo = Db::name('user_team_relation')
                ->field('team_id')
                ->where('user_id', $userInfo['uid'])
                ->select();

            if (Auth::createSession($userInfo, $teamInfo)) {
                // 记录登陆日志
                $logData = [
                    'uid' => $userInfo['uid'],
                    'ip'  => get_client_ip(),
                ];

                // 记录日志
                $log = new UserLog;
                $res = $log->allowField(true)->save($logData);
                return $this->response(200, Lang::get('Success login'));
            } else {
                return $this->response(201, Lang::get('Fail login'));
            }
        }

        return $this->fetch('login', ['lang' => Cookie::get('think_var')]);
    }
}
