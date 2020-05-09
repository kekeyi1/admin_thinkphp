<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/31
 * Time: 16:33
 */

namespace app\common\logic;

use app\common\service\Status;
use app\common\service\Common;

class Performance
{
    /**
     * 格式化参数
     *
     * @param $data
     * @return array
     */
    public static function format($data){
        $search = $param = [];
        //时间范围
        if (!empty($data['account_time'])) {
            $account_time = explode(' - ', $data['account_time']);
            $param['account_time'] = [strtotime(current($account_time)), strtotime(end($account_time)) + 86400 - 1];
            $search['account_time'] = $data['account_time'];
        } else {
            $param['account_time'] = Common::getTime();
            $search['account_time'] = '';
        }

        if (!empty($data['archive_time'])) {
            $create_time = explode(' - ', $data['archive_time']);
            $param['archive_time'] = [strtotime(current($create_time)), strtotime(end($create_time)) + 86400 - 1];
            $search['archive_time'] = $data['archive_time'];
        } else {
            $param['archive_time'] = '';
            $search['archive_time'] = '';
        }

        $search['area'] = '';
        $search['state'] = '';
        if (!empty($data['area'])) {
            $param['area'] = $data['area'];
            $search['area'] = $data['area'];
        }

        if (!empty($data['state'])) {
            $param['state'] = $data['state'];
            $search['state'] = $data['state'];
        }

        //执行案号、创建人、承办法官、标的物名称、法院名称
        $search['value'] = '';
        $value = '';
        if (!empty($data['value'])) {
            $value = trim($data['value']);
            $search['value'] = trim($data['value']);
        }

        return [$search, $param, $value];
    }

    /**
     * 格式化参数
     *
     * @param $data
     * @return array
     */
    public static function formatParam($data)
    {
        $search = $param = [];
        //时间范围
        if (!empty($data['account_time'])) {
            $account_time = explode(' - ', $data['account_time']);
            $param['account_time'] = [strtotime(current($account_time)), strtotime(end($account_time)) + 86400 - 1];
            $search['account_time'] = $data['account_time'];
        } else {
            $param['account_time'] = Common::getTime();
            $search['account_time'] = '';
        }

        if (!empty($data['archive_time'])) {
            $create_time = explode(' - ', $data['archive_time']);
            $param['archive_time'] = [strtotime(current($create_time)), strtotime(end($create_time)) + 86400 - 1];
            $search['archive_time'] = $data['archive_time'];
        } else {
            $param['archive_time'] = '';
            $search['archive_time'] = '';
        }

        $search['is_sure'] = '';
        if (!empty($data['is_sure'])) {
            $param['smpd.is_sure'] = $data['is_sure'] == 1 ? 1 : 0;
            $search['is_sure'] = $data['is_sure'];
        }

        $search['state'] = '';
        if (!empty($data['state'])) {
            $param['smp.state'] = intval($data['state']);
            $search['state'] = intval($data['state']);
        }

        //执行案号、创建人、承办法官、标的物名称、法院名称
        $search['value'] = '';
        $search['area'] = '';
        $value = '';
        if (!empty($data['value'])) {
            $value = trim($data['value']);
            $search['value'] = trim($data['value']);
        }

        return [$search, $param, $value];
    }

    /**
     * 格式化数据
     *
     * @param $data
     * @return mixed
     */
    public static function formatData($data)
    {
        $state = array_column(Status::$performance_type, null, 'id');
        foreach ($data as $key => &$item) {
            if (empty($item)) continue;
            $item['id']           = bcadd($key, 1);
            $item['account_time'] = date('Y-m-d H:i', $item['account_time']);
            $item['subject_matter_number'] = ' ' . $item['subject_matter_number'];
            if (isset($state[$item['state']])) {
                $data[$key]['state'] = $state[$item['state']]['name'];
            }
        }
        unset($item);
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function formatExportData($data)
    {
        $state = array_column(Status::$performance_type, null, 'id');

        foreach ($data as $key=>&$item) {
            if (empty($item)) continue;
            $item['id']                    = bcadd($key, 1);
            $item['subject_matter_number'] = ' ' . $item['subject_matter_number'];
            $item['account_time']          = date('Y-m-d H:i', $item['account_time']);
            $item['type']                  = $item['type'] == Status::MANAGE_COMMISSION ? '管理提成' : '团队提成';
            $item['is_sure']               = $item['is_sure'] ? '是' : '否';
            if (isset($state[$item['state']])) {
                $item['state'] = $state[$item['state']]['name'];
            }
        }
        unset($item);
        return $data;
    }
}