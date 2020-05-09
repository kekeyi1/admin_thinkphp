<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/27
 * Time: 15:12
 */

namespace app\common\model;

use app\common\service\Status;
use think\Model;
use think\Db;
use app\common\model\AuthRole;

class Performance extends Model
{
    protected $pk = 'id';
    // 设置当前模型对应的完整数据表名称
    protected $table = 'subject_matter_performance';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条业绩提成
     *
     * @param mixed $id
     * @return mixed
     */
    public static function getPerformance($id)
    {
        return Performance::get(['id', $id]);
    }

    /**
     * 更新提成状态
     *
     * @param $id
     * @param $state
     * @param bool $boole
     * @param bool $isValid
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateState($id, $state, $boole = false)
    {
        $data = [
            'state' => $state,
            'id' => $id,
        ];
        if ($boole) {
            $data['archive_time'] = time();
        }
        return Db::name('subject_matter_performance')->update(['state' => $state, 'id' => $id]);
    }
}