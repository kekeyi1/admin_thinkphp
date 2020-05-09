<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 14:44
 */

namespace app\common\model;

use think\Db;
use think\Model;
use app\common\model\AuthRole;

class FlowType extends Model
{
    protected $pk = 'id';
    // 设置当前模型对应的完整数据表名称
    protected $table = 'workflow_type';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
    }
}