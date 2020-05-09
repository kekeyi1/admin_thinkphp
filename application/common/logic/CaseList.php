<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/20
 * Time: 16:28
 */

namespace app\common\logic;

use app\common\service\Status;
use app\common\service\Common;
use app\common\service\AuthRole;

class CaseList
{
    /**
     * 参数验证
     *
     * @param $data
     * @return array
     */
    public static function formatParam($data)
    {
        $search = $param = [];
        //时间范围
        if (empty($data['create_time'])) {
            $param['create_time']  = Common::getTime();
            $search['create_time'] = '';
        } else {
            $create_time           = explode(' - ', $data['create_time']);
            $param['create_time']  = [strtotime(current($create_time)), strtotime(end($create_time)) + 86399];
            $search['create_time'] = $data['create_time'];
        }

        //案件状态
        $search['state'] = '';
        $param['state'] = [Status::CASE_TO_PROCESSED, Status::CASE_PROCESSING, Status::CASE_COMPLETION];
        if (!empty($data['state'])) {
            $param['state']  = $search['state'] = $data['state'];
        }

        //数据范围
        if (!isset($data['data_range'])){
            $data['data_range'] = Status::TEAM;
        }

        if (!empty($data['data_range'])) {
            $param['data_range'] = $search['data_range'] = $data['data_range'];
        }

        //执行案号、创建人、承办法官、标的物名称、法院名称
        $search['value'] = $param['value'] = '';
        if (!empty($data['value'])) {
            $param['value'] = $search['value'] = trim($data['value']);
        }

        return [$search, $param];
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function formatExportData($data)
    {
        $state = array_column(Status::$case_state, null, 'id');

        foreach ($data as $key=>&$item) {
            if (empty($item)) continue;
            $item['id'] = bcadd($key, 1);
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            if (isset($state[$item['state']])) {
                $item['state'] = $state[$item['state']]['name'];
            }
        }
        unset($item);
        return $data;
    }

    /**
     * 验证区域经理权限
     *
     * @param $role_ids
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function checkPermissions($role_ids)
    {
        $permissions = false;
        $role = AuthRole::getRoleByRoleName('区域经理');

        //区域经理撤案权限
        if (!empty($role)) {
            if (in_array($role['role_id'], explode(',', $role_ids))) {
                $permissions = true;
            }
        }
        return $permissions;
    }
}