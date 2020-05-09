<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/12
 * Time: 15:15
 */

namespace app\common\service;

use  app\common\model\SubjectMatterState as SubjectMatterStateModel;
use app\common\service\Status;
use think\Db;

class SubjectMatterState
{
    /**
     * 获取状态
     *
     * @param $type
     * @return array
     */
    public static function getList($type)
    {
        return Db::name('subject_matter_state')
            ->where('type', $type)
            ->where('isvalid', Status::IS_VALID)
            ->column('id, name');
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public static function getStateList($type)
    {
        if (empty($type)) return [];
        $list = SubjectMatterStateModel::all(
            [
                'type' => $type,
                'isvalid' => Status::IS_VALID
            ]
        );

        return $list;
    }

    /**
     * 获取拍卖成交状态ID
     *
     * @return mixed
     */
    public static function getClinchDealState()
    {
        return Db::name('subject_matter_state')
            ->where('type', status::AUCTION_STATE)
            ->where('isvalid', Status::IS_VALID)
            ->where('name', '成交')
            ->value('id');
    }
}