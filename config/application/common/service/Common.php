<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/16
 * Time: 9:58
 */

namespace app\common\service;


class Common
{
    /**
     * 生成唯一编号
     *
     * @param int $unique
     * @return string
     */
    public static function getUniqueId($unique = 0)
    {
        $orderNo = date('YmdHi') . substr(microtime(), 2, 3) . mt_rand(100, 999);
        return $orderNo;
    }

    /**
     * 获取最近3个月的时间戳
     *
     * @param int $param
     * @return array
     */
    public static function getTime($param = 3)
    {
        $current_time = time();
        $last_time    = strtotime("-$param month");
        return [$last_time, $current_time];
    }

    /**
     * 根据节点返回状态
     *
     * @param $node
     * @return int
     */
    public static function getPerformanceWorkflowStateByStep($node)
    {
        $state = 0;
        switch ($node) {
            case 1:
                $state = 1;
                break;
            case 2:
                $state = 2;
                break;
            case 3:
                $state = 3;
                break;
            case 4:
                $state = 4;
                break;
            case 5:
                $state = 5;
                break;
            case 6:
                $state = 6;
                break;
            case 7:
                $state = 7;
                break;
            default;
        }

        return $state;
    }

    /**
     * 计算管理提成金额 团队提成金额
     *
     * @param $amount
     * @return array
     */
    public static function formatCommissionAmount($amount)
    {
        $management_commission = bcmul($amount, 0.15, 2);
        $team_commission       = bcmul($amount, 0.85, 2);
        return [$management_commission, $team_commission];
    }
}