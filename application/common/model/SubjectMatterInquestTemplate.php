<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/9
 * Time: 14:41
 */

namespace app\common\model;

use app\common\service\Status;
use think\Db;
use think\Model;
use think\facade\Lang;

class SubjectMatterInquestTemplate extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 查询模板列表
     *
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getTemplateList(){
        return Db::name('subject_matter_inquest_template')
            ->alias('smit')
            ->join('auth_user au','au.uid = smit.create_uid')
            ->field('smit.*,au.username')
            ->order('smit.id desc')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
            ]);
    }
}