<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/3
 * Time: 16:00
 */

namespace app\common\service;

use think\Db;

class AuthRole
{
    /**
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRoleList()
    {
        $list = Db::name('auth_role')->whereIn('role_name', ['内勤', '外勤', '管理员'])->select();
        return $list;
    }

    /**
     * 根据角色名获取角色信息
     *
     * @param $role_name
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRoleByRoleName($role_name)
    {
        $info = Db::name('auth_role')->where('role_name', $role_name)->find();
        return $info;
    }
}