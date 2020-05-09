<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/2
 * Time: 18:11
 */

namespace app\common\model;

use think\Db;
use think\Model;
use app\common\service\Status;

class CaseList extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCase($id)
    {
        return CaseList::where('id', $id)->field('id,create_uid')->find();
    }

    /**
     * 更新案件状态
     *
     * @param $id
     * @param $state
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateState($id, $state)
    {
        return CaseList::where('id', $id)->update('state', $state);
    }
}