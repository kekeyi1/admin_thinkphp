<?php
/**
 * User: yangyujuan
 * Date: 2019/12/10
 * Time: 10:33
 */

namespace app\common\model;

use think\Model;
use think\Db;

class SubjectMatterState extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}