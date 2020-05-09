<?php
namespace app\common\behavior;

use think\Controller;
use think\Db;
use app\common\model\UserLog;

class User extends Controller
{
    public function userLoginLog($params)
    {
        // 记录日志
        $log = new UserLog;
        return $res = $log->allowField(true)->save($params);
    }
}