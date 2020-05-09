<?php
/**
 * User: yangyujaun
 * Date: 2019/12/17
 * Time: 9:00
 */

namespace app\common\service;

use app\common\model\Dept as DeptModel;
use think\Db;

class Dept
{
    /**
     * 查询部门(根据公司id)
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public static function getDeptList($agency_id)
    {
        return Db::name('dept')
            ->where('agency_id', $agency_id)
            ->where('isvalid', Status::IS_VALID)
            ->order('id')
            ->select();
    }

}