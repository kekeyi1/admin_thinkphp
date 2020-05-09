<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/12
 * Time: 17:43
 */

namespace app\common\service;

use app\common\model\SubjectMatterPropertyType as SubjectMatterPropertyTypeModel;
use think\Db;

class SubjectMatterPropertyType
{
    /**
     * 获取财产类型
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        return Db::name('subject_matter_property_type')
            ->where('isvalid', Status::IS_VALID)
            ->column('id, name');
    }

    /**
     * 获取财产类型
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPropertyTypeList()
    {
        $list = Db::name('subject_matter_property_type')
            ->where('isvalid', Status::IS_VALID)
            ->field('id, id as value, name, pid')
            ->select();
        if (empty($list)) return [];
        $res = static::formatTree($list);

        return $res;
    }

    /**
     * 无限极财产类型分类
     *
     * @param $array
     * @param int $pid
     * @return array
     */
    public static function formatTree($array, $pid = 0)
    {
        $res = $tem = [];
        foreach ($array as $item) {
            if ($item['pid'] == $pid) {
                //判断是否存在子数组
                $tem = self::formatTree($array, $item['value']);
                $tem && $item['children'] = $tem;
                $res[] = $item;
            }
        }
        return $res;
    }

    /**
     * 获取财产类型一级二级
     *
     * @param bool $type
     * @return array
     */
    public static function getPropertyTypeByLevel($type = true)
    {
        if ($type) {
            return Db::name('subject_matter_property_type')
                ->where('isvalid', Status::IS_VALID)
                ->where('pid', 0)
                ->column('id');
        }

        return Db::name('subject_matter_property_type')
            ->where('isvalid', Status::IS_VALID)
            ->where('pid','>', 0)
            ->column('id');
    }
}