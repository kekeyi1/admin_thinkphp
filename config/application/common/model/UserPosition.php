<?php
/**
 * User: yangyujuan
 * Date: 2019/12/10
 * Time: 16:00
 */

namespace app\common\model;

use think\Db;
use think\Model;

class UserPosition extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}