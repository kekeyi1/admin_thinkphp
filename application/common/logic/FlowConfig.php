<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/26
 * Time: 17:23
 */

namespace app\common\logic;

use think\Db;

class FlowConfig
{
  public static function workflowLimit($id,$subject_matter_id,$method){
        $case = Db::name('case_list')->where('id',$id)->select();
        $subject_matter = Db::name('subject_matter_id')->where('id',$subject_matter_id)->select();
        $invoice = Db::name('subject_matter_invoice')->where('id',$subject_matter_id)->select();

        $result = [];   
       switch($method){
           case 1:
               if($this->uid != $subject_matter['create_uid']){
                return $result = ['status' => 0 , 'msg' => '只有团队长才可以分配明细！'];
               }
               if($subject_matter['state']!=5){
                return $result = ['status' => 0 , 'msg' => '拍卖未完成！'];
               }
               if($invoice['state']!=5){
                return $result = ['status' => 0 , 'msg' => '未开发票！'];
               }

           break;
           case 2:

           break;
           case 3:

           break;
           default;
       }


       return ['status'=> 1, 'msg' => 'success!'];
  }
}