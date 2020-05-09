<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/2
 * Time: 17:29
 */

namespace app\common\logic;

use  app\common\service\AuthUser;

class Customer
{
    public static $sex = [
        '1' => '男',
        '2' => '女',
        '3' => '保密',
    ];

    public static $customer_state = [
        '1' => '了解产品',
        '2' => '正在跟进',
        '3' => '准备购买',
        '4' => '准备付款',
        '5' => '已经购买',
        '6' => '暂时搁置',
    ];

    public static $intention_product = [
        '1' => '抵押贷',
        '2' => '消费贷',
    ];

    /**
     * 格式化参数
     *
     * @param $data
     * @param $uid
     * @return array
     */
    public static function formatParam($data, $uid)
    {
        $search = $param = [];
        //客户状态
        if (!empty($data['state'])) {
            $search['state'] = $param['c.state'] = $data['state'];
        } else {
            $search['state'] = '';
        }

        //数据范围
        if (!empty($data['data_range'])) {
            $search['data_range'] = $data['data_range'];
            if ($data['data_range'] == 1){
                $param['c.create_uid'] = $uid;
            }
        } else {
            $search['data_range']  = 2;
        }

        //模糊搜索
        if (!empty($data['value'])) {
            $search['value'] = $param['c.value'] = $data['value'];
        } else {
            $search['value'] = '';
            $param['c.value'] = '';
        }

        return array($search, $param);
    }

    /**
     * 格式化导出数据
     *
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function formatData($data)
    {
        $user_list = AuthUser::getList();
        $user_list = array_column($user_list, 'name', 'id');
        foreach ($data as $key=>&$item) {
            if (empty($item)) continue;
            $item['id'] = bcadd($key, 1);
            if (isset(static::$sex[$item['sex']])) {
                $item['sex'] = static::$sex[$item['sex']];
            }
            if (isset(static::$customer_state[$item['state']])) {
                $item['state'] = static::$customer_state[$item['state']];
            }
            if (isset(static::$intention_product[$item['intention_product']])) {
                $item['intention_product'] = static::$intention_product[$item['intention_product']];
            }
            if ($user_list[$item['referees']]) {
                $item['referees'] = $user_list[$item['referees']];
            }
            if ($user_list[$item['create_uid']]) {
                $item['create_uid'] = $user_list[$item['create_uid']];
            }
            if (empty($item['sm_name'])){
                $item['sm_name'] = '';
            }
            $item['create_time'] = date('Y-m-d', $item['create_time']);
        }
        unset($item);
        return $data;
    }
}