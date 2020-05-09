<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/16
 * Time: 11:12
 */

namespace app\common\service;

use think\Db;
use app\common\model\Region as RegionModel;

class Region
{
    /**
     * 查询地址
     *
     * @param $ids
     * @return array
     */
    public static function getList($ids)
    {
        if (empty($ids)) return [];
        return Db::name('region')
            ->where('id', 'in', $ids)
            ->where('isvalid', Status::IS_VALID)
            ->column('id,name');
    }

    /**
     * 获取所有二级城市
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCityList()
    {
        return Db::name('region')
            ->where('level', Status::LEVEL)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->select();
    }

    /**
     * 获取南京市所有区属
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNanJingArea()
    {
        //查询南京市区域
        $id = Db::name('region')->where('name', Status::NAN_JING)
            ->value('id');

        //根据南京市ID查询南京市区属
        $list = Db::name('region')->where('pid', $id)
            ->where('isvalid', Status::IS_VALID)
            ->field('id,name')
            ->select();

        return $list;
    }
}