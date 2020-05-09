<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/6
 * Time: 11:45
 */

namespace app\common\service;

use think\Db;
use think\facade\Lang;
use app\common\model\Performance as PerformanceModel;

class Performance
{
    /**
     * 发票归档创建提成分配数据
     *
     * @param $id
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function create($id, $uid)
    {
        $invoice = Db::name('subject_matter_invoice')->where('id', $id)->find();
        if (empty($invoice)) return ['code' => 201, 'message' => Lang::get('Error')];
        $create_Info = Db::table('case_list')->where('id', $invoice['case_id'])->field('create_uid,team_id')->find();
        if (isset($create_Info['create_uid']) && $create_Info['create_uid']!= $uid) return ['code' => 201, 'message' => Lang::get('No permissions')];
        $performance = Db::table('subject_matter_performance')->where('case_id', $invoice['case_id'])
            ->where('subject_matter_id', $invoice['subject_matter_id'])
            ->find();
//        if (!empty($performance)) return ['code' => 201, 'message' => Lang::get('This record already exists')];
        //查询团队ID
        $userInfo = Db::name('auth_user')
            ->alias('au')
            ->join('user_team_relation utr', 'au.uid = utr.user_id')
            ->join('user_team ut', 'ut.id =utr.team_id')
            ->where('au.uid', $uid)
            ->where('ut.id', $create_Info['team_id'])
            ->field('ut.id')
            ->find();

        if (empty($userInfo)) return ['code' => 201, 'message' => Lang::get('Error')];
        //查询团队成员
        $teamInfo = Db::name('auth_user')
            ->alias('au')
            ->join('user_team_relation utr', 'au.uid = utr.user_id')
            ->join('user_team ut', 'ut.id =utr.team_id')
            ->join('user_position up', 'up.id =au.position_id', 'left')
            ->where('ut.id', $userInfo['id'])
            ->field('au.uid user_id,ut.id,up.id position_id,utr.is_leader')
            ->group('utr.team_id,utr.user_id,utr.is_leader')
            ->select();

        $data = [
            'account_amount'    => $invoice['invoice_price'],
            'receivable_amount' => isset($invoice['receivable_amount']) ? $invoice['receivable_amount'] : '',
            'commission_amount' => bcmul($invoice['invoice_price'], 0.06, 2),
            'state'             => Status::TO_BE_DISTRIBUTED,
            'team_id'           => $userInfo['id'],
            'case_id'           => $invoice['case_id'],
            'subject_matter_id' => $invoice['subject_matter_id'],
            'create_uid'        => $uid,
            'account_time'      => time(),
        ];

        // 启动事务
        Db::startTrans();
        try {
            $insert_id = Db::table('subject_matter_performance')->insertGetId($data);
            $teamInfo = static::formatData($teamInfo, $insert_id);
            $rows = Db::table('subject_matter_performance_distribution')->insertAll($teamInfo);
            if (empty($insert_id) || empty($rows)) {
                // 回滚事务
                Db::rollback();
                return ['code' => 201, 'message' => Lang::get('Fail')];
            }
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
     * @param $insert_id
     * @return mixed
     */
    public static function formatData($data, $insert_id)
    {
        $res = [];
        foreach ($data as $key => $item) {
            if (empty($item['position_id'])) continue;
            $res[$key] = [
                'user_id'        => $item['user_id'],
                'is_sure'        => Status::NOT_SURE,
                'weight'         => 0,
                'amount'         => 0,
                'create_time'    => time(),
                'position_id'    => $item['position_id'],
                'performance_id' => intval($insert_id),
            ];
            if ($item['is_leader'] == Status::IS_LEADER) {
                $res[$key]['type'] = Status::IS_LEADER;
            } else {
                $res[$key]['type'] = Status::IS_TEAM;
            }
        }

        return $res;
    }
}