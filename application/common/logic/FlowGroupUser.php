<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/26
 * Time: 10:14
 */

namespace app\common\logic;


class FlowGroupUser
{
    /**
     * 批量增加流程组人员
     *
     * @param $data
     * @return array
     */
    public static function formatData($data)
    {
        $uids = explode(',', $data['uid']);
        $res = [];
        foreach ($uids as $item) {
            $res[] = [
                'workflow_group_id' => $data['workflow_group_id'],
                'uid'               => $item,
                'isvalid'           => $data['isvalid'],
                'create_uid'        => $data['create_uid'],
            ];
        }

        return $res;
    }
}