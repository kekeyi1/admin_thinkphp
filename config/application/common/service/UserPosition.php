<?php


namespace app\common\service;

use think\Db;

class UserPosition
{
    /**
     * 查询地址
     *
     * @param $ids
     * @return array
     */
    public static function getList()
    {
        return Db::name('user_position')
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->select();
    }
}