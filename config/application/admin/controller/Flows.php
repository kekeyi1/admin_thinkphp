<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 14:43
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\service\Status;
use app\common\service\FlowType;
use app\common\service\FlowConfig;
use app\common\service\FlowVersion;
use app\common\logic\Flow as FlowLogic;
use app\common\model\Flow as FlowModel;
use app\common\service\Flow as FlowService;
use app\common\model\PerformanceDistribution;
use app\common\model\Performance as PerformanceModel;
use app\common\service\FlowGroupUser as FlowGroupUserService;

class Flows extends Admin
{
    /**
     * 获取流程节点
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//    public function getList()
//    {
//        $obj = new  FlowModel;
//        $list = $obj->getCaseFlowList(8);
//
//        return $this->response(200, Lang::get('Success'), $list);
//    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function gitConfig()
    {
        $request = Request::param();

        $flowVersionId = '';
        //查询流程类型是否存在
        $flowType = FlowType::getFlowTypeById($request['type_id']);
        if (!empty($flowType)) {
            //查询流程版本
            $flowVersion = FlowVersion::getFlowVersionByTypeId($flowType['id']);
            if (empty($flowVersion)) {
                return $this->response(201, Lang::get('Tips one'));
            }
            //验证流程版本个数
            if (count($flowVersion) == 1) {
                $flowVersionId = current($flowVersion)['id'];
            } else {
                $sign = 0;
                foreach ($flowVersion as $value) {
                    $sql_text = $value['sql_text'];
                    if ($sql_text) {
                        $sqlResult = Db::query($sql_text);

                        if ($sqlResult) {
                            $sign++;
                            $flowVersionId = $value['id'];
                        }
                    }

                    if ($sign > 1) {
                        return $this->response(201, Lang::get('Tips two'));
                    }
                }
            }
        }
        //根据流程类型ID和流程版本ID查询流程配置
        $flowConfig = FlowConfig::getFlowConfigByTypeIdAndVersionId($flowType['id'], $flowVersionId);

        if (empty($flowConfig)) {
            return $this->response(201, Lang::get('Tips three'));
        }

        //判断是否有权限
        $this_group_id = session('userinfo.group_id');
        $flowConfig = current($flowConfig);
        if ($flowConfig['type'] && isset($flowConfig['role_id']) && isset($this_group_id) && !in_array($flowConfig['role_id'], $this_group_id)) {
            return $this->response(201, Lang::get('Tips four'));
        }

        $data = array(
            'workflow_type_id' => $flowConfig['workflow_type_id'],
            'workflow_version_id' => $flowConfig['workflow_version_id'],
            'step' => $flowConfig['step'],
            'subject_matter_id' => $flowConfig['subject_matter_id'],
            'type' => $flowConfig['type']
        );

        return $this->response(201, Lang::get('Tips four'), $data);
    }

    /**
     * 获取流程步骤
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWorkflowStep()
    {
        $request = Request::param();
        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->join('auth_user au', 'au.uid = wfs.user_id', 'left')
            ->where('wf.id', $request['workflow_id'])
            ->where('wf.isvalid', Status::IS_VALID)
            ->field('wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,au.true_name,wfs.is_back')
            ->order('wfs.accept_time')
            ->select();

        if (empty($list)) return $this->response(201, Lang::get('No flow'));

        $list = FlowLogic::formatData($list);
        return $this->response(200, Lang::get('Success'), $list);
    }

    /**
     * 流程节点下一步
     *
     * @return array|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function nextStep()
    {
        $request = Request::param();

        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->where('wf.case_id', $request['case_id'])
            ->where('wfs.isvalid', Status::IS_VALID)
            ->where('wfs.is_back', Status::NOT_BACK)
            ->where('wf.subject_matter_id', $request['subject_matter_id'])
            ->field('wf.state,wf.id,wf.workflow_type_id,wf.workflow_version_id,wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,wf.step')
            ->order('wf.step desc')
            ->select();

        $current = current($list);
        if (empty($current)) return $this->response(201, Lang::get('Fail add'));

        //流程完成 提示完成
        if ($current['step'] == 5) {
            return $this->response(201, Lang::get('Archived'));
        }

        //获取下一步
        $step = $current['step'] + 1;
        $config = FlowService::getWorkflowConfigByStep($current, $step);
        if (!isset($config)) return $this->response(201, Lang::get('Fail add'));

        //流程步骤对接人判断，个人
        if ($config['type'] == Status::PERSONAL) {
            if ($this->uid != $config['role_id']) {
                return $this->response(201, Lang::get('No permissions'));
            }
        }

        //流程组
        if ($config['type'] == Status::GROUP) {
            $roleIds = FlowGroupUserService::getGroupUserList($config['role_id']);
            if (!in_array($this->uid, $roleIds)) {
                return $this->response(201, Lang::get('No permissions'));
            }
        }

        $flowStep = [
            'workflow_id' => $current['id'],
            'workflow_node_id' => $config['workflow_node_id'],
            'workflow_config_id' => $config['id'],
            'user_id' => $this->uid,
            'is_back' => Status::NOT_BACK,
            'isvalid' => Status::IS_VALID,
            'accept_time' => time(),
        ];
        $performance = Db::name('subject_matter_performance')->where('workflow_id', $current['id'])->value('id');

        // 启动事务
        Db::startTrans();
        try {
            //更新流程状态，更新业绩提成状态
            if (!empty($current)) {
                switch ($step) {
                    case 1:
                        FlowModel::updateState($current['id'], Status::TO_BE_CONFIRMED, $step);
                        break;
                    case 2:
                        FlowModel::updateState($current['id'], Status::APPLYING, $step);
                        break;
                    case 3:
                        FlowModel::updateState($current['id'], Status::THE_TRIAL_PASS, $step);
                        PerformanceModel::updateState($performance, Status::THE_TRIAL_PASS);
                        break;
                    case 4:
                        FlowModel::updateState($current['id'], Status::THE_REVIEW_PASS, $step);
                        PerformanceModel::updateState($performance, Status::THE_REVIEW_PASS);
                        break;
                    case 5:
                        FlowModel::updateState($current['id'], Status::THE_ARCHIVE, $step);
                        PerformanceModel::updateState($performance, Status::THE_ARCHIVE,true);
                        break;
                    default;
                }
            }

            $insertId = Db::name('workflow_step')->insertGetId($flowStep);
            // 提交事务
            Db::commit();
            if (is_numeric($insertId)) {
                return $this->response(200, Lang::get('Success pass'));
            } else {
                return $this->response(201, Lang::get('Fail pass'));
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->response(201, Lang::get('Fail pass'));
        }
    }

    /**
     * 流程驳回
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function returnBack()
    {
        $request = Request::param();
        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->where('wf.case_id', $request['case_id'])
            ->where('wfs.isvalid', Status::IS_VALID)
            ->where('wfs.is_back', Status::NOT_BACK)
            ->where('wf.subject_matter_id', $request['subject_matter_id'])
            ->field('wf.state,wf.id,wf.workflow_type_id,wf.workflow_version_id,wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,wf.step')
            ->order('wf.step desc')
            ->select();

        $current = current($list);
        //判断是否完成
        if ($current['step'] == 5) {
            return $this->response(201, Lang::get('Archived'));
        }
        if (empty($current)) return $this->response(201, Lang::get('Fail add'));
        //获取下一步
        $step = $current['step'] + 1;
        //判断是否是退回步骤 第三第四步可以驳回 其它不能驳回 并且状态为申请中 初审通过
        if (in_array($step, [3, 4])) {
            $config = FlowService::getWorkflowConfigByStep($current, $step);
            //流程步骤对接人判断，个人
            if ($config['type'] == Status::PERSONAL) {
                if ($this->uid != $config['role_id']) {
                    return $this->response(201, Lang::get('No permissions'));
                }
            }

            //流程组
            if ($config['type'] == Status::GROUP) {
                $roleIds = FlowGroupUserService::getGroupUserList($config['role_id']);
                if (!in_array($this->uid, $roleIds)) {
                    return $this->response(201, Lang::get('No permissions'));
                }
            }

            if (!isset($config)) return $this->response(201, Lang::get('Fail'));
            $performance = Db::name('subject_matter_performance')->where('workflow_id', $current['id'])->field('id,state')->find();
            if (!in_array($performance['state'],[3, 4])){
                return $this->response(201, Lang::get('Tips seven'));
            }

            $distribution = Db::name('subject_matter_performance_distribution')->where('performance_id', $performance['id'])->select();
            $distribution = FlowLogic::formatDistribution($distribution);

            // 启动事务
            Db::startTrans();
            try {
                //第三步、第四部初审驳回、复审驳回
                switch ($step) {
                    case 3:
                        FlowModel::updateState($current['id'], Status::THE_TRIAL_REJECTED, $current['step'],true);
                        PerformanceModel::updateState($performance['id'], Status::THE_TRIAL_REJECTED);
                        break;
                    case 4:
                        FlowModel::updateState($current['id'], Status::THE_REVIEW_REJECTED, $current['step'],true);
                        PerformanceModel::updateState($performance['id'], Status::THE_REVIEW_REJECTED);
                        break;
                }
                $obj = new PerformanceDistribution;
                $obj->saveAll($distribution);
                //组装步骤数据
                $flowStep = [
                    'workflow_id' => $current['id'],
                    'workflow_node_id' => $config['workflow_node_id'],
                    'workflow_config_id' => $config['id'],
                    'user_id' => $this->uid,
                    'is_back' => Status::IS_BACK,
                    'isvalid' => Status::NOT_VALID,
                    'accept_time' => time(),
                    'remark' => $request['remark'],
                ];

                $insertId = Db::name('workflow_step')->insertGetId($flowStep);
                // 提交事务
                Db::commit();
                if (is_numeric($insertId)) {
                    return $this->response(200, Lang::get('Success return'));
                } else {
                    return $this->response(201, Lang::get('Fail return'));
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->response(201, $e);
            }
        } else {
            return $this->response(201, Lang::get('Tips seven'));
        }
    }

    /**
     * 流程节点下一步
     *
     * @return array|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function nextStepFlow()
    {
        $request = Request::param();
        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->where('wf.case_id', $request['case_id'])
            ->where('wfs.isvalid', Status::IS_VALID)
            ->where('wfs.is_back', Status::NOT_BACK)
            ->where('wf.subject_matter_id', $request['subject_matter_id'])
            ->field('wf.state,wf.id,wf.workflow_type_id,wf.workflow_version_id,wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,wf.step')
            ->order('wf.step desc')
            ->select();

        $current = current($list);
        if (empty($current)) return $this->response(201, Lang::get('Fail add'));
        //todo 判断是否完成
        if ($current['step'] == 5) {
            return $this->response(201, Lang::get('Archived'));
        }

        //获取下一步
        $step = $current['step'] + 1;
        $config = FlowService::getWorkflowConfigByStep($current, $step);
        if (!isset($config)) return $this->response(201, Lang::get('Fail add'));
        $flowStep = [
            'workflow_id' => $current['id'],
            'workflow_node_id' => $config['workflow_node_id'],
            'workflow_config_id' => $config['id'],
            'user_id' => $this->uid,
            'is_back' => Status::NOT_BACK,
            'isvalid' => Status::IS_VALID,
            'accept_time' => time(),
        ];

        //TODO 处理业务逻辑
        
        $insertId = Db::name('workflow_step')->insertGetId($flowStep);
        if (is_numeric($insertId)) {
            return $this->response(200, Lang::get('Success add'));
        } else {
            return $this->response(201, Lang::get('Fail add'));
        }
    }

    /**
     * 开票驳回
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function returnBackFlow()
    {
        $request = Request::param();
        //查询案件标的物流程
        $list = Db::name('workflow')
            ->alias('wf')
            ->join('workflow_step wfs', 'wf.id = wfs.workflow_id')
            ->join('workflow_node wfn', 'wfn.id = wfs.workflow_node_id', 'left')
            ->where('wf.case_id', $request['case_id'])
            ->where('wfs.isvalid', Status::IS_VALID)
            ->where('wfs.is_back', Status::NOT_BACK)
            ->where('wf.subject_matter_id', $request['subject_matter_id'])
            ->field('wf.state,wf.id,wf.workflow_type_id,wf.workflow_version_id,wfs.user_id uid,wfs.accept_time,wfn.name,wfs.remark,wf.step')
            ->order('wf.step desc')
            ->select();

        $current = current($list);
        if (empty($current)) return $this->response(201, Lang::get('Fail add'));
        //获取下一步
        $step = $current['step'] + 1;
        //判断是否是退回步骤
        //todo
        if (in_array($step, [5, 4])) {
            $config = FlowService::getWorkflowConfigByStep($current, $step);
            if (!isset($config)) return $this->response(201, Lang::get('Fail add'));
            //更新业绩提成状态步骤
//            $state = Common::getPerformanceWorkflowStateByStep($step);
            //第三步、第四部初审驳回、复审驳回 todo 更新状态
            switch ($step) {
                case 3:
                    FlowModel::updateState($current['id'], Status::THE_TRIAL_REJECTED, $step);
                    break;
                case 4:
                    FlowModel::updateState($current['id'], Status::THE_REVIEW_REJECTED, $step);
                    break;
            }

            //todo 组装步骤数据
            $flowStep = [
                'workflow_id' => $current['id'],
                'workflow_node_id' => $config['workflow_node_id'],
                'workflow_config_id' => $config['id'],
                'user_id' => $this->uid,
                'is_back' => Status::IS_BACK,
                'isvalid' => Status::NOT_VALID,
                'accept_time' => time(),
            ];

            $insertId = Db::name('workflow_step')->insertGetId($flowStep);
            if (is_numeric($insertId)) {
                return $this->response(200, Lang::get('Success return'));
            } else {
                return $this->response(201, Lang::get('Fail return'));
            }
        } else {
            return $this->response(201, Lang::get('Tips seven'));
        }
    }
}
