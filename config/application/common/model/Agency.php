<?php
/**
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 10:33
 */

namespace app\common\model;

use think\Model;
use think\Db;
use app\common\service\Status;

class Agency extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 公司类型
    protected $type = array(
        Status::AGENT => "法拍公司",
        Status::COURT => "法院",
    );
    
    /**
     * 获取公司类型
     * @return mixed|\think\response\Json
     */
    public function getType()
    {
        return $this->type;
    }  
}