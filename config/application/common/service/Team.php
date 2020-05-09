<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/9
 * Time: 14:19
 */

namespace app\common\service;

use think\Db;

class Team
{
    /**
     * 根据用户ID和法院编号获取用户团队信息
     *
     * @param $uid
     * @param $court_id
     * @return mixed|string
     */
    public static function getTeamInfoByUserAndCourt($uid, $court_id)
    {
        if (empty($uid) || empty($court_id)) return '';

        $info = Db::table('auth_user')
            ->alias('au')
            ->join('user_team_relation utr', 'utr.user_id = au.uid')
            ->join('user_team ut', 'utr.team_id = ut.id')
            ->where('utr.user_id', $uid)
            ->where('ut.isvalid', Status::IS_VALID)
            ->where('ut.agency_id', $court_id)
            ->value('utr.team_id');

        return $info;
    }
}