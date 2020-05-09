<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/3
 * Time: 15:44
 */

namespace app\common\model;

use think\Db;
use think\Model;
use app\common\service\Status;

class Customer extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $table = 'customer';

    protected $createTime = 'create_time';

}