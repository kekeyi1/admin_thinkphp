<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/18
 * Time: 14:18
 */

namespace app\common\model;

use app\common\service\Status;
use think\Db;
use think\Model;
use think\facade\Lang;

class SubjectMatterPerformance extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}