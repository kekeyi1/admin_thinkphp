<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/30
 * Time: 11:16
 */

namespace app\common\service;

use think\Db;
use think\facade\Lang;
use app\common\model\SubjectMatter;
use app\common\model\Flow as FlowModel;
use app\common\model\SubjectMatterBase;
use app\common\service\Flow as FlowService;
use app\common\model\PerformanceDistribution as PerformanceDistributionModel;

class Flow
{
    /**
     * @param $type_id
     * @param $subject_matter_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function gitConfig($type_id, $subject_matter_id)
    {
        $flowVersionId = '';
        //查询流程类型是否存在
        $flowType = FlowType::getFlowTypeById($type_id);
        if (!empty($flowType)) {
            //查询流程版本
            $flowVersion = FlowVersion::getFlowVersionByTypeId($flowType['id']);
            if (empty($flowVersion)) {
                return ['code' => 201, 'message' => Lang::get('Tips one')];
            }
            //验证流程版本个数
            if (count($flowVersion) == 1) {
                $flowVersionId = current($flowVersion)['id'];
            } else {
                return ['code' => 201, 'message' => Lang::get('Tips two')];
            }
        }
        //根据流程类型ID和流程版本ID查询流程配置
        $flowConfig = FlowConfig::getFlowConfigByTypeIdAndVersionId($type_id, $flowVersionId);
        $flowConfig = current($flowConfig);

        if (empty($flowConfig)) {
            return ['code' => 201, 'message' => Lang::get('Tips three')];
        }

        //返回数据格式
        $data = array(
            'workflow_type_id'    => $type_id,
            'workflow_version_id' => $flowConfig['workflow_version_id'],
            'step'                => $flowConfig['step'],
            'subject_matter_id'   => $subject_matter_id,
            'type'                => $flowConfig['type']
        );

        return ['code' => 200, 'message' => Lang::get('Success'), 'data' => $data];
    }

    /**
     * 创建流程
     *
     * @param $data
     * @param array $arr
     * @param array $commission
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create($data, $arr, $commission)
    {
        $obj = new SubjectMatter;
        $baseObj = new SubjectMatterBase;
        $subjectMatter = $obj->getSubjectMatterById($data['subject_matter_id']);
        $subjectMatterBase = $baseObj->getSubjectMatterBaseById($data['subject_matter_id']);
        $flow = [
            'case_id'             => $subjectMatter['case_id'],
            'subject_matter_id'   => $data['subject_matter_id'],
            'workflow_version_id' => $data['workflow_version_id'],
            'workflow_type_id'    => $data['workflow_type_id'],
            'isvalid'             => Status::IS_VALID,
            'step'                => $data['step'],
            'state'               => Status::NORMAL,
            'create_uid'          => $data['uid'],
            'city_id'             => $subjectMatterBase['city_id'],
            'create_time'         => time(),
        ];

        // 启动事务
        Db::startTrans();
        try {
            //新建流程
            $workflowInsertId = Db::name('workflow')->insertGetId($flow);
            $config = static::getWorkflowConfigByStep($data, Status::CREATE);
            //流程步骤数据
            $flowStep = [
                'workflow_id'        => $workflowInsertId,
                'workflow_node_id'   => $config['workflow_node_id'],
                'workflow_config_id' => $config['id'],
                'user_id'            => $data['uid'],
                'is_back'            => Status::NOT_BACK,
                'isvalid'            => Status::IS_VALID,
                'accept_time'        => time(),
            ];
            //插入流程步骤
            $workflowStepInsertId = Db::name('workflow_step')->insertGetId($flowStep);
            //接受数据
            list($managementAmount, $teamAmount, $distributionBalance) = $commission;
            //组装数据格式，提成管理
            $updateData = [
                'state'                 => Status::TO_BE_CONFIRMED,
                'management_commission' => $managementAmount,
                'team_commission'       => $teamAmount,
                'distribution_balance'  => $distributionBalance,
                'workflow_id'           => $workflowInsertId,
            ];
            //更新提成分配结算情况
            $effect_rows = Db::name('subject_matter_performance')->where('id', $data['performance_id'])->update($updateData);
            //批量更新分配数据
            foreach ($arr as $item) {
                $updateRows = Db::name('subject_matter_performance_distribution')->update($item);
                if (empty($updateRows)) {
                    // 回滚事务
                    Db::rollback();
                    return ['code' => 201, 'message' => Lang::get('Fail')];
                }
            }
            //验证数据是否添加成功
            if (empty($workflowInsertId) || empty($workflowStepInsertId) || empty($effect_rows)) {
                // 回滚事务
                Db::rollback();
                return ['code' => 201, 'message' => Lang::get('Fail')];
            }
            // 提交事务
            Db::commit();
            //返回数据
            return ['code' => 200, 'message' => Lang::get('Success')];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return ['code' => 201, 'message' => Lang::get('Fail')];
        }
    }

    /**
     *开票创建流程
     *
     * @param $data
     * @param $uid
     * @param $id
     * @param $type
     * @param array $array1
     * @param array $array2
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createFlow($data, $uid, $id, $type, $array1 = [], $array2 = [])
    {
        $obj = new SubjectMatter;
        $subject_matter = $obj->getSubjectMatterById($data['subject_matter_id']);
        $flow = [
            'case_id' => $subject_matter['case_id'],
            'subject_matter_id' => $data['subject_matter_id'],
            'workflow_version_id' => $data['workflow_version_id'],
            'workflow_type_id' => $data['workflow_type_id'],
            'city_id' => 1, //todo
            'isvalid' => Status::IS_VALID,
            'step' => $data['step'],
            'state' => Status::NORMAL,
            'create_uid' => $uid,
            'create_time' => time(),
        ];
        $workflowStepInsertId = 0;
        // 启动事务
        Db::startTrans();
        try {
            $workflowInsertId = Db::name('workflow')->insertGetId($flow);
            $config = static::getWorkflowConfigByStep($data, Status::CREATE);

            $flowStep = [
                'workflow_id' => $workflowInsertId,
                'workflow_node_id' => $config['workflow_node_id'],
                'workflow_config_id' => $config['id'],
                'user_id' => $uid,
                'is_back' => 0,
                'isvalid' => Status::IS_VALID,
                'accept_time' => time(),
            ];

            $workflowStepInsertId = Db::name('workflow_step')->insertGetId($flowStep);
            //todo

            // 提交事务
            Db::commit();
            return ['code' => 200, 'message' => Lang::get('Success')];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return ['code' => 201, 'message' => Lang::get('Fail')];
        }
    }

    /**
     * @param $data
     * @param $step
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getWorkflowConfigByStep($data, $step)
    {
        $map = [
            'workflow_type_id' => $data['workflow_type_id'],
            'workflow_version_id' => $data['workflow_version_id'],
            'step' => $step,
            'isvalid' => Status::IS_VALID,
        ];

        //配置节点信息
        $info = Db::name('workflow_config')
            ->where($map)
            ->find();

        return $info;
    }

    /**
     * 业绩提成下一步通用 人员确认步骤
     * 流程节点下一步
     *
     * @param $workflow_id
     * @param $uid
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function nextStep($workflow_id, $uid)
    {
        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->where('wf.id', $workflow_id)
            ->where('wfs.isvalid', Status::IS_VALID)
            ->where('wfs.is_back', Status::NOT_BACK)
            ->field('wf.state,wf.id,wf.workflow_type_id,wf.workflow_version_id,wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,wf.step')
            ->order('wf.step desc')
            ->select();
        //获取最新步骤
        $current = current($list);
        if (empty($current)) return ['code' => 201, 'message' => Lang::get('Fail')];
        //第三步
        $step = 3;
        $config = FlowService::getWorkflowConfigByStep($current, $step);
        if (!isset($config)) return ['code' => 201, 'message' => Lang::get('Fail')];

        $flowStep = [
            'workflow_id' => $current['id'],
            'workflow_node_id' => $config['workflow_node_id'],
            'workflow_config_id' => $config['id'],
            'user_id' => $uid,
            'is_back' => Status::NOT_BACK,
            'isvalid' => Status::IS_VALID,
            'accept_time' => time(),
        ];
        //插入流程步骤
        $insertId = Db::name('workflow_step')->insertGetId($flowStep);
        if (is_numeric($insertId)) {
            return ['code' => 200, 'message' => Lang::get('Success'), 'data' => $current];
        } else {
            return ['code' => 201, 'message' => Lang::get('Fail')];
        }
    }
}