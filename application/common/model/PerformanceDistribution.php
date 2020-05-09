<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/27
 * Time: 13:55
 */

namespace app\common\model;

use think\Model;
use think\Db;
use app\common\model\AuthRole;

class PerformanceDistribution extends Model
{
    protected $pk = 'id';
    // 设置当前模型对应的完整数据表名称
    protected $table = 'subject_matter_performance_distribution';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
    }

}