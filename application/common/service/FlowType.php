<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 14:12
 */

namespace app\common\service;

use app\common\model\FlowType as FlowTypeModel;

class FlowType
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
        return FlowTypeModel::where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->order('id desc')
            ->select();
    }

    /**
     * 根据ID获取单条记录
     *
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFlowTypeById($id)
    {
        if (empty($id)) return [];

        return FlowTypeModel::where('id', $id)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->find();
    }
}