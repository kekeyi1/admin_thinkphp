<?php

namespace app\common\model;

use think\Model;
use think\facade\Lang;

class UserLog extends Model
{
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_at';

    public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 查询用户登录日志
     *
     * @param $id
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getUserLoginLog($id)
    {
        return UserLog::field('l.*,u.username')
            ->alias('l')
            ->where('l.uid', $id)
            ->join('auth_user u', 'l.uid = u.uid')
            ->order('l.id desc')
            ->paginate(Lang::get('Paginate'), false, [
                'type'     => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
            ]);
    }

    /**
     * 管理查看登录日志
     *
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getSuperLoginLog()
    {
        return UserLog::field('l.*,u.username')
            ->alias('l')
            ->where('l.uid', '>=', 1)
            ->join('auth_user u', 'l.uid = u.uid')
            ->order('l.id desc')
            ->paginate(Lang::get('Paginate'), false, [
                'type'     => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
            ]);
    }
}