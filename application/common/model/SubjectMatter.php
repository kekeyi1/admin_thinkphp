<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 14:44
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\facade\Lang;
use app\common\service\Status;

class SubjectMatter extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 获取标的物列表
     *
     * @param $request
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getSubjectMatterList($request)
    {
        $where = [
            'sm.case_id' => $request
        ];
        return Db::name('subject_matter')
            ->alias('sm')
            ->where($where)
            ->join('subject_matter_base smb', 'sm.id = smb.subject_matter_id')
            ->join('auth_user au', 'au.uid=create_uid')
            ->order('sm.id desc')
            ->field('sm.*,smb.property_type_first,smb.property_type_second,smb.auction_state,au.true_name username')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
            ]);
    }

    /**
     * 获取单条标的物
     *
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubjectMatterById($id)
    {
        return Db::name('subject_matter')
            ->where('id', $id)
            ->field('id,case_id,create_uid')
            ->find();
    }
}