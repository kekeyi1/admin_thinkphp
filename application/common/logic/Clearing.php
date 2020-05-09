<?php
/**
 * User: yangyujuan
 * Date: 2019/12/30
 * Time: 15:16
 */

namespace app\common\logic;

use app\common\service\Status;
use app\common\service\Common;
use think\Db;

class Clearing
{
    /**
     * 参数验证
     *
     * @param $data
     * @return array
     */
    public static function formatParam($data)
    {
        $search = $param = [];
        //时间范围
        if (empty($data['deal_time'])) {
            $param['deal_time']  = '';
            $search['deal_time'] = '';
        } else {
            $deal_time           = explode(' - ', $data['deal_time']);
            $param['deal_time']  = [strtotime(current($deal_time)), strtotime(end($deal_time))];
            $search['deal_time'] = $data['deal_time'];
        }

        //申请状态
        $search['apply_state'] = '';
        $param['apply_state'] = [1, 2];
        if (!empty($data['apply_state'])) {
            $param['apply_state']  = $data['apply_state'];
            $search['apply_state'] = $data['apply_state'];
        }
        
        //法院名称、执行案号、标的物名称、标的物编号
        $search['value'] = '';
        $param['value']  = '';
        if (!empty($data['value'])) {
            $param['value']  = trim($data['value']);
            $search['value'] = trim($data['value']);
        }

        return [$search, $param];
    }

    /**
     * 导出
     * @param $data
     * @return mixed
     */
    public static function formatExportData($data)
    {
        $state = array_column(Status::$apply_state, null, 'id');
        foreach ($data as &$item) {
            $item['deal_time'] = date('Y-m-d H:i',$item['deal_time']);
            $item['subject_matter_number'] = " ".$item['subject_matter_number']." ";
            if (isset($state[$item['apply_state']])) {
                $item['apply_state'] = $state[$item['apply_state']]['name'];
            }
        }
        unset($item);
        return $data;
    }
    
    /**
     * 计算应收金额
     * @param $data
     * @return mixed
     */
    public static function receivable_amountData($data)
    {
        $subject_matter_id = $data["subject_matter_id"];
        $result = 0;
        if(!empty($subject_matter_id)){ 
            $property_type_first = Db::name('subject_matter_base')->where('id',$subject_matter_id)->value("property_type_first");
            if($property_type_first == 16){
                if($data["service_charge"]<1000000){
                    $result = $data["service_charge"]*0.003;
                }else{
                    $result = $data["service_charge"]*0.001;
                }
            }else{
                if($data["service_charge"]<50000){
                    $result = $data["service_charge"]*0.019;
                }else{
                    $result = $data["service_charge"]*0.001;
                }
            }
        }
        return $result;
    }
}