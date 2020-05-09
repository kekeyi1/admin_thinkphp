<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 17:25
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\facade\Lang;

class SubjectMatterBase extends Model
{
    protected $pk = 'id';

    /**
     * 获取单条标的物
     *
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubjectMatterBaseById($id)
    {
        return Db::name('subject_matter_base')
            ->where('subject_matter_id', $id)
            ->field('id,city_id')
            ->find();
    }
}