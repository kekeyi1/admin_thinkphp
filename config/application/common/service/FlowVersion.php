<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/25
 * Time: 14:22
 */

namespace app\common\service;

use app\common\model\FlowVersion as FlowVersionModel;

class FlowVersion
{
    /**
     * 获取流程类型版本列表
     *
     * @param $workflow_type_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList($workflow_type_id)
    {
        // 使用查询构造器查询满足条件的数据
        return FlowVersionModel::where('isvalid', Status::IS_VALID)
            ->where('workflow_type_id', $workflow_type_id)
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
    public static function getFlowVersionByTypeId($id)
    {
        if (empty($id)) return [];

        // 使用查询构造器查询满足条件的数据
        return FlowVersionModel::where('workflow_type_id', $id)
            ->where('isvalid', Status::IS_VALID)
            ->select()
            ->toArray();
    }
}