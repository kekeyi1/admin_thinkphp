<?php
/**
 * User: yangyujuan
 * Date: 2019/3/11
 * Time: 9:00
 */
namespace app\admin\controller;

use think\Db;
use think\Controller;
use think\facade\Log;
use Qiniu\Http\Client;
use app\common\service\Status;
use app\common\model\CaseList;
use app\common\service\SubjectMatterState;
use app\common\model\SubjectMatterAcution as SubjectMatterAcutionModel;

class TaskScheduler extends Controller
{  
    /**
     * 订时查询拍卖信息的状态
     * @return mixed
     * @throws \think\exception\DbException
     * http://admin.tianqifapai.com/admin/task_scheduler/acutionupdate.html
     */
    public function acutionUpdate()
    {
        $obj = new SubjectMatterAcutionModel;
        $where['isvalid'] = 1;
        $list = $obj->where($where)->select();
        foreach($list as $value){
            if(!empty($value['arsis_address'])){
                //返回拍卖信息状态等字段
                $return = $this->getAcutionState($value['arsis_address']);
                if(!empty($return)){
                    $return['id'] = $value['id'];
                    //更新拍卖信息表
                    $obj->isUpdate(true)->allowField(true)->save($return);
                }
            }
        }
        echo '更新成功！';
    }
    
    /**
     * 返回拍卖信息状态$data
     * @param string $url
     * @return $data
     * @throws \think\exception\DbException
     */
    private function getAcutionState($url)
    {
        $return_body = "";
        $data = array();
        $request = htmlspecialchars_decode(trim($url));
        $return = Client::get($request);
        if($return->statusCode==-1){
            return $this->response(201, Lang::get('Acution address fail'));
        }
        //交易状态
        $reg=  "/<h1 class=\"bid-fail\">.*?<\/h1>/ism";
        if(preg_match_all($reg, $return->body, $matches_body)){
            $return_body = $matches_body[0][0];
        }
        $return_body = strip_tags(trim($return_body));
        $encode = mb_detect_encoding($return_body, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
        $return_body = iconv($encode,'UTF-8',$return_body);
        //查询交易状态
        $transaction_state = SubjectMatterState::getStateList(Status::TRANSACTION_STATE);
        foreach($transaction_state as $value){
            //成交结束文字转换
            if($value['name']=="成交"){
                $state = "结束";   
            } else {
                $state = $value['name'];
            }
            if(strstr($return_body, $state)){
                $data['transaction_state'] = $value['id'];
            } 
        }
        //交易成功时的成交金额和成交时间
        if(!empty($data['transaction_state']) && $data['transaction_state']==15 ){
            //成交金额
            $reg1 = "/<span class=\"pm-current-price J_Price\">.*?<\/em>/ism"; 
            if(preg_match_all($reg1, $return->body, $matches)){
                $data['deal_price'] = trim(strip_tags($matches[0][0]));
            }
            //成交时间
            $reg2 = "/<span  class=\"countdown J_TimeLeft\">.*?<\/span>/ism";
            if(preg_match_all($reg2, $return->body, $matches)){
                $data['deal_time'] = strtotime(trim(strip_tags($matches[0][0])));
            } 
        }
        return $data;
    }

    /**
     * 查询需要更新的案件状态
     *
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * http://admin.tianqifapai.com/admin/task_scheduler/updatecasestate.html
     */
    public function updateCaseState()
    {
        $list = Db::table('case_list')
            ->alias('cl')
            ->join('subject_matter sm', 'cl.id = sm.case_id')
            ->join('subject_matter_base smb', 'sm.id = smb.subject_matter_id')
            ->where('cl.state', '<>', Status::CASE_COMPLETION)
            ->where('smb.auction_state', SubjectMatterState::getClinchDealState())
            ->field('cl.*,sm.id as sm_id')
            ->group('cl.id')
            ->select();

        $res = $this->formatData($list);
        Log::record('案件状态更新');
        if (!empty($res)) {
            $obj = new CaseList();
            $obj->saveAll($res);
        }

        echo '案件状态更新成功';
    }

    /**
     * 格式化案件数据
     *
     * @param $data
     * @return array
     */
    protected function formatData($data)
    {
        $res = [];
        foreach ($data as $item) {
            if (empty($item)) continue;
            $res[] = [
                'id' => $item['id'],
                'state' => Status::CASE_COMPLETION
            ];
        }

        return $res;
    }
}