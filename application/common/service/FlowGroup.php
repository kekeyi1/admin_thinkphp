<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/26
 * Time: 8:46
 */

namespace app\common\service;

use think\Db;

class FlowGroup
{
    /**
     * 获取流程组
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        return Db::name('workflow_group')
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->order('id desc')
            ->select();
    }
}