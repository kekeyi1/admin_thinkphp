<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/2
 * Time: 15:23
 */

namespace app\common\logic;

use app\common\service\Common;

class PerformanceStatistics
{
    /**
     * 格式化参数处理
     *
     * @param $data
     * @return array
     */
    public static function formatParam($data)
    {
        $search = $param = [];
        //时间范围
        if (empty($data['account_time'])) {
            $param['account_time'] = Common::getTime(12);
            $search['account_time'] = '';
        } else {
            $account_time = explode(' - ', $data['account_time']);
            $param['account_time'] = [strtotime(current($account_time)), strtotime(end($account_time))];
            $search['account_time'] = $data['account_time'];
        }

        //执行案号、创建人、承办法官、标的物名称、法院名称
        $search['value'] = '';
        $param['value'] = '';
        if (!empty($data['value'])) {
            $param['value'] = trim($data['value']);
            $search['value'] = trim($data['value']);
        }

        return [$search, $param];
    }

    /**
     * 格式化返回数据
     *
     * @param $data
     * @return array
     */
    public static function formatData($data)
    {
        if (empty($data)) return [];
        foreach ($data as $key=>&$item) {
            if (empty($item)) continue;
            $item['id']                    = bcadd($key, 1);
            $item['subject_matter_number'] = ' ' . $item['subject_matter_number'];
            $item['account_amount']        = floatval($item['account_amount']);
            $item['weight']                = floatval($item['weight']);
            $item['commission_amount']     = floatval($item['commission_amount']);
            $item['amount']                = floatval($item['amount']);
            $item['account_time']          = date('Y-m-d', $item['account_time']);
        }
        unset($item);
        return $data;
    }
}