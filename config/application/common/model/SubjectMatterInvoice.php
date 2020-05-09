<?php
/**
 * User: yangyujuan
 * Date: 2020/1/3
 * Time: 10:33
 */

namespace app\common\model;

use think\Model;
use think\Db;

class SubjectMatterInvoice extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'apply_time';
   
}