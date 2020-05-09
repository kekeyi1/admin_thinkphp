<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 14:39
 */

namespace app\common\service;

use app\common\model\FlowNode as FlowNodeModel;

class FlowNode
{
    /**
     * 获取节点类型列表
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        return FlowNodeModel::where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->order('id desc')
            ->select();
    }
}