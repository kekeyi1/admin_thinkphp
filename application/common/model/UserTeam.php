<?php
/**
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 10:33
 */

namespace app\common\model;

use think\Db;
use think\Model;

class UserTeam extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}