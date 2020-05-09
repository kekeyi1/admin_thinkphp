<?php

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\model\UserLog as LogModel;

class Log extends Admin
{
    /**
     * 登录日志
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $log = new LogModel;
        $role_ids = explode(',', $this->role_ids);
        //超级管理员
        if (in_array(1, $role_ids)) {
            $list = $log->getSuperLoginLog();
        } else {
            $list = $log->getUserLoginLog($this->uid);
        }

        $data = [
            'log'         => $list,
            'current_num' => Common::formatPage($list->currentPage(), $list->listRows()),
        ];

        return $this->fetch('index', $data);
    }

    /**
     * 登录日志
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function handle()
    {
        $request = Request::instance()->param();

        switch ($request['type']) {
            case 'delete':
                $result = Db::name('log')->where('site_id', $this->site_id)->delete();
                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
        }

        return $this->response(201, Lang::get('Fail'));
    }
}
