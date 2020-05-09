<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 14:44
 */

namespace app\common\model;

use think\Db;
use think\Model;
use app\common\service\Status;
use app\common\model\AuthRole;

class Flow extends Model
{
    protected $pk = 'id';
    // 设置当前模型对应的完整数据表名称
    protected $table = 'workflow';

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
     * 获取流程节点
     *
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCaseFlowList($id)
    {
        return Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->where('wf.case_id', $id)
            ->field('wf.*,wfs.*')
            ->order('wfs.id desc')
            ->select();
    }

    /**
     * @param $id
     * @param $state
     * @param null $step
     * @param null $isValid
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateState($id, $state, $step = null, $isValid = null)
    {
        $where = [
            'state' => $state
        ];
        if (isset($step)) $where['step'] = $step;
        if (isset($isValid)) $where['isvalid'] = Status::NOT_VALID;
        return Db::name('workflow')->where('id', $id)->update($where);
    }
}