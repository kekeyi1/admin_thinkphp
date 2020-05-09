<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/7
 * Time: 18:23
 */

namespace app\common\service;

use think\Db;

class FlowGroupUser
{
    /**
     * 获取流程组人员
     *
     * @param $id
     * @return array
     */
    public static function getGroupUserList($id)
    {
        return Db::name('workflow_group_user')
            ->where('workflow_group_id', $id)
            ->column('uid');
    }
}