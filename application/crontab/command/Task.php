<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/9
 * Time: 9:44
 */

namespace app\crontab\command;

use think\Db;
use think\facade\Log;
use think\console\Input;
use think\console\Output;
use think\console\Command;
use app\common\service\Status;
use app\common\model\CaseList;
use think\console\input\Option;
use think\console\input\Argument;
use app\common\service\SubjectMatterState;

class Task extends Command
{
    /**
     * 配置任务名称，任务描述
     */
    protected function configure()
    {
        $this->setName('Task')->setDescription('定时计划：每天根据标的状态更新案件状态');
    }

    /**
     * 执行脚本
     *
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('Crontab job start...');
        Log::record('进程开始时间');
        $this->updateState();
        $output->writeln('Crontab job end...');
    }

    /**
     * 查询需要更新的案件状态
     *
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function updateState()
    {
        $list = Db::table('case_list')
            ->alias('cl')
            ->join('subject_matter sm', 'cl.id = sm.case_id')
            ->join('subject_matter_base smb', 'sm.id = smb.subject_matter_id')
            ->where('cl.state', '<>', Status::CASE_COMPLETION)
            ->where('smb.auction_state', SubjectMatterState::getClinchDealState())
            ->field('cl.*,sm.id as sm_id')
            ->group('cl.id')
            ->select();

        $res = $this->formatData($list);
        Log::record('案件状态更新');
        if (!empty($res)) {
            $obj = new CaseList();
            $obj->saveAll($res);
        }
        
        return true;
    }

    /**
     * 格式化案件数据
     *
     * @param $data
     * @return array
     */
    protected function formatData($data)
    {
        $res = [];
        foreach ($data as $item) {
            if (empty($item)) continue;
            $res[] = [
                'id' => $item['id'],
                'state' => Status::CASE_COMPLETION
            ];
        }

        return $res;
    }
}