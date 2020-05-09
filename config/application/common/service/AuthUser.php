<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/13
 * Time: 15:00
 */

namespace app\common\service;

use app\common\model\AuthUser as AuthUserModel;
use think\Db;


class AuthUser
{
    /**
     * 查询法院旗下承办法官
     *
     * @param $court_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserList($court_id)
    {
        if (empty($court_id)) return [];
        return Db::name('auth_user')
            ->alias('au')
            ->join('agency a', 'a.id = au.agency_id')
            ->where('a.id', $court_id)
            ->where('a.type', Status::COURT)
//            ->where('au.position_id', Status::COURT)
            ->order('au.uid desc')
            ->field('au.uid id, au.true_name username')
            ->select();
    }

    /**
     * 查询用户信息
     *
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserInfo($user_id)
    {
        return Db::name('auth_user')
            ->where('uid', $user_id)
            ->where('status', Status::USER_IS_VALID)
            ->field('uid id,username,true_name')
            ->find();
    }

    /**
     * 查询用户列表
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        return Db::name('auth_user')
            ->alias('au')
//            ->join('agency a', 'a.id = au.agency_id')
            ->where('au.status', Status::NOT_VALID)
            ->order('au.uid desc')
            ->field('au.uid id, au.true_name as name')
            ->select();
    }
}