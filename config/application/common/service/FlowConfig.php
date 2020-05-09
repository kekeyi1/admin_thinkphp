<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/26
 * Time: 17:20
 */

namespace app\common\service;

use think\Db;

class FlowConfig
{
    /**
     * todo
     * 获取流程组
     *
     * @param $type
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList($type)
    {
        return Db::name('workflow_config')
            ->where('isvalid', Status::IS_VALID)
            ->where('type', $type)
//            ->field('id,name')
            ->order('id desc')
            ->select();
    }

    /**
     * 根据流程类型和流程版本获取流程配置
     *
     * @param $type_id
     * @param $version_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFlowConfigByTypeIdAndVersionId($type_id, $version_id)
    {
        if (empty($type_id) || empty($version_id)) return [];

        return Db::name('workflow_config')
            ->where('isvalid', Status::IS_VALID)
            ->where('workflow_type_id', $type_id)
            ->where('workflow_version_id', $version_id)
            ->order('step asc')
            ->select();
    }
}