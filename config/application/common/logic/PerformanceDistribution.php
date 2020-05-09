<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/30
 * Time: 10:31
 */

namespace app\common\logic;


use app\common\service\Status;

class PerformanceDistribution
{
    /**
     * @param $data
     * @param $distribution
     * @return array
     */
    public static function formatTotal($data, $distribution)
    {
        $new = [
            'management_commission'     => 0,
            'management_amount'         => 0,
            'management_weight'         => 0,
            'management_surplus_amount' => 0,
            'team_commission'           => 0,
            'team_amount'               => 0,
            'team_weight'               => 0,
            'team_surplus_amount'       => 0,
        ];
        $total_management_commission = floatval(bcmul($data['commission_amount'], 0.15, 2));
        $total_team_commission = floatval(bcmul($data['commission_amount'], 0.85, 2));
        foreach ($distribution as $item) {
            if ($item['type'] == Status::IS_LEADER) {
                $new['management_commission'] = $data['management_commission'];
                $new['management_amount'] = floatval($item['amount']);
                $new['management_weight'] = floatval(bcsub(100,$item['weight'],2));
//                $new['management_surplus_amount'] = floatval(bcsub($data['management_commission'], floatval($item['amount']), 2));
                $new['management_surplus_amount'] = floatval(bcsub($total_management_commission, $data['management_commission'], 2));
                continue;
            }
            $new['team_commission'] = $data['team_commission'];
            $new['team_amount'] = floatval($item['amount']);
            $new['team_weight'] = floatval(bcsub(100,$item['weight'],2));
//            $new['team_surplus_amount'] = floatval(bcsub($data['team_commission'], floatval($item['amount']), 2));
            $new['team_surplus_amount'] = floatval(bcsub($total_team_commission, $data['team_commission'], 2));
        }
        return $new;
    }

    /**
     * 处理权重格式
     *
     * @param $data
     * @return mixed
     */
    public static function formatData($data)
    {
        foreach ($data as &$item) {
            $item['weight'] = floatval($item['weight']);
            $item['amount'] = floatval($item['amount']);
        }
        unset($item);
        return $data;
    }

    /**
     * @param $data
     * @param $tmp
     * @return mixed
     */
    public static function formatParam($data, $tmp)
    {
        $manage = $team = 0;
        foreach ($data as $item) {
            if ($item['type'] == Status::MANAGE_COMMISSION) {
                $manage += $tmp[$item['id']]['weight'];
                continue;
            }

            $team += $tmp[$item['id']]['weight'];
        }
        if ($manage > 100 || $team > 100) return false;

        return true;
    }

    /**
     * 分别计算管理提成 团队提成
     *
     * @param $distribution
     * @param $management_commission
     * @param $team_commission
     * @param $tmp
     * @param $commission_amount
     * @return array
     */
    public static function calculateCommission($distribution, $management_commission, $team_commission, $tmp, $commission_amount)
    {
        $arr = [];
        $managementAmount = $teamAmount = 0;
        foreach ($distribution as $key => $item) {
            if (isset($tmp[$item['id']]) && !empty($tmp[$item['id']]['weight'])) {
                if ($item['type'] == Status::IS_LEADER) {
                    $arr[$key] = [
                        'id' => $item['id'],
                        'state' => Status::IS_DISTRIBUTION,
                        'weight' => floatval($tmp[$item['id']]['weight']),
                        'amount' => floatval(bcmul($management_commission, bcdiv($tmp[$item['id']]['weight'], 100, 2), 2))
                    ];
                    $managementAmount = bcadd($managementAmount, $arr[$key]['amount'], 2);
                } else {
                    $arr[$key] = [
                        'id' => $item['id'],
                        'state' => Status::IS_DISTRIBUTION,
                        'weight' => floatval($tmp[$item['id']]['weight']),
                        'amount' => floatval(bcmul($team_commission, bcdiv($tmp[$item['id']]['weight'], 100, 2), 2))
                    ];
                    $teamAmount = bcadd($teamAmount, $arr[$key]['amount'], 2);
                }
            }
        }
        $total = bcadd($managementAmount, $teamAmount, 2);
        $distributionBalance = bcsub($commission_amount, $total, 2);
        return [$arr, [$managementAmount, $teamAmount, $distributionBalance]];
    }
}