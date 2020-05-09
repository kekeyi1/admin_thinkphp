<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/13
 * Time: 11:31
 */

namespace app\common\service;

use app\common\model\Agency as AgencyModel;
use think\Db;

class Agency
{
    /**
     * 查询中介公司、法院
     *
     * @param $type
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAgencyList($type)
    {
        return Db::name('agency')
            ->where('type', $type)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->order('id')
            ->select();
    }

    /**
     * 查询中介公司、法院
     *
     * @param $uid
     * @param $type
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAgencyListByUid($uid, $type)
    {
        return Db::table('auth_user')
            ->alias('au')
            ->join('user_team_relation utr', 'utr.user_id = au.uid')
            ->join('user_team ut', 'utr.team_id = ut.id')
            ->join('agency a', 'ut.agency_id = a.id')
            ->where('utr.user_id', $uid)
            ->where('ut.isvalid', Status::IS_VALID)
            ->where('a.type', $type)
            ->field('a.id,a.name')
            ->group('a.id')
            ->order('a.id')
            ->select();
    }

    /**
     * 查询法院信息
     *
     * @param $court_id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCourtInfo($court_id)
    {
        return Db::name('agency')
            ->where('id', $court_id)
            ->where('type', Status::COURT)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name,province_id,city_id,area_id')
            ->find();
    }

    /**
     * 查询公司信息
     *
     * @param $court_id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAgentInfo($court_id)
    {
        return Db::name('agency')
            ->where('id', $court_id)
            ->where('type', Status::AGENT)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->find();
    }
}