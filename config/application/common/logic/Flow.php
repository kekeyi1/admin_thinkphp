<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/30
 * Time: 16:08
 */

namespace app\common\logic;


use app\common\service\Status;

class Flow
{
    /**
     * @param $data
     * @return mixed
     */
    public static function formatData($data)
    {
        foreach ($data as &$item) {
            $item['accept_time'] = date('Y-m-d H:i', $item['accept_time']);
            if (isset($item['is_back']) && $item['is_back'] == Status::IS_BACK) {
                $item['name'] = $item['name'] . "（驳回）";
            }
        }

        return $data;
    }

    /**
     * 格式化分配数据，重新分配
     *
     * @param $data
     * @return array
     */
    public static function formatDistribution($data)
    {
        $new = [];
        foreach ($data as $item) {
            $new[] = [
                'id' => $item['id'],
                'weight' => 0,
                'amount' => 0,
                'is_sure' => 0,
                'state' => 0,
            ];
        }
        return $new;
    }
}