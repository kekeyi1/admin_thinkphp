<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/5
 * Time: 17:25
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\facade\Lang;

class SubjectMatterImage extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

}