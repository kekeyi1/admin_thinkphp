<?php
/**
 * Created by Wamp.
 * User: yangyujuan
 * Date: 2019/12/20
 * Time: 16:10
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\facade\Lang;

class SubjectMatterAcution extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 拍卖次数
    protected $acution_number = array(
        1 => "一拍",
        2 => "二拍",
        3 => "变卖",
    );
    
    // 拍卖次数
    protected $publish_address = array(
        "taobao" => "阿里法拍",
        "jd" => "京东"
    );
    
    // 获得拍卖次数
    public function getAcutionNumber(){
        return $this->acution_number;
    }  
    
     // 获得拍卖次数
    public function getPublishAddress(){
        return $this->publish_address;
    }  
    
}