<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/10
 * Time: 15:02
 */

namespace app\common\logic;


class Team
{
    /**
     * 返回数据格式
     *
     * @param $user_id
     * @param $username
     * @param $team_id
     * @param $type
     * @param $is_leader
     * @param $create_uid
     * @return array
     */
    public static function formatData($user_id, $username, $team_id, $type, $is_leader, $create_uid)
    {
        return [
            'user_id'    => $user_id,
            'username'   => $username,
            'team_id'    => $team_id,
            'type'       => $type,
            'is_leader'  => $is_leader,
            'create_uid' => $create_uid,
        ];
    }
}