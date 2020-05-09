<?php
namespace app\admin\controller;

//use app\common\controller\admin;
use think\Db;
use workflow\workflow;
use think\facade\Session;

class wf extends Admin {
    public function initialize()
    {
        parent::initialize();
        $this->work = new workflow();
        $session = Session::get('user_auth', 'admin');
        $this->uid = $session['uid'];
        $this->role = $session['role_ids'];;
        $this->Tmp  = '../extend/workflow/view/';
    }
    /**
     * 流程设计首页
     * @param $map 查询参数
     */
    public function wfindex($map = []){
        $this->assign('list',$this->work->FlowApi('List'));
        $this->assign('type', ['subject_matter_invoice' => '结算管理', 'subject_matter_performance' => '提成管理']);
        return  $this->fetch();
    }
    /**
     * 工作流设计界面
     */
    public function wfdesc(){

        $flow_id = intval(input('flow_id'));
        if($flow_id<=0){
            $this->error('参数有误，请返回重试!');
        }
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
            $this->error('未找到数据，请返回重试!');
        }
        $this->assign('one', $one);
        $this->assign('process_data',$this->work->ProcessApi('All',$flow_id));
        return $this->fetch();
    }
    /**
     * 流程添加
     */
    public function wfadd()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $data['uid']=session('uid');
            $data['add_time']=time();
            $ret= $this->work->FlowApi('AddFlow',$data);
            if($ret['code']==0){
                return msg_return('发布成功！');
            }else{
                return msg_return($ret['data'],1);
            }
        }
//        $this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
        $this->assign('type', ['subject_matter_invoice' => '结算管理', 'subject_matter_performance' => '提成管理']);
        return  $this->fetch();
    }
    /**
     * 流程修改
     */
    public function wfedit()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $ret= $this->work->FlowApi('EditFlow',$data);
            if($ret['code']==0){
                return msg_return('修改成功！');
            }else{
                return msg_return($ret['data'],1);
            }
        }
        if(input('id')){
            $this->assign('info', $this->work->FlowApi('GetFlowInfo',input('id')));
        }
//        $this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
        $this->assign('type', ['subject_matter_invoice' => '结算管理', 'subject_matter_performance' => '提成管理']);
        return $this->fetch('wfadd');
    }
    /**
     * 状态改变
     */
    public function wfchange()
    {
        if ($this->request->isGet()) {
            $data = ['id'=>input('id'),'status'=>input('status')];
            $ret= $this->work->FlowApi('EditFlow',$data);
            if($ret['code']==0){
                $this->success('操作成功',url('wf/wfindex'));
            }else{
                $this->error('操作失败！',url('wf/wfindex'));
            }
        }
    }

    /**
     * 删除流程
     **/
    public function delete_process()
    {
        return json($this->work->ProcessApi('ProcessDel',input('flow_id'),input('process_id')));
    }
    public function del_allprocess()
    {
        return json($this->work->ProcessApi('ProcessDelAll',input('flow_id')));
    }
    /**
     * 添加流程
     **/
    public function add_process()
    {
        $flow_id = input('flow_id');
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
            return json(['status'=>0,'msg'=>'添加失败,未找到流程','info'=>'']);
        }
        return json($this->work->ProcessApi('ProcessAdd',$flow_id));
    }
    /**
     * 保存布局
     **/
    public function save_canvas()
    {
        return json($this->work->ProcessApi('ProcessLink',input('flow_id'),input('process_info')));
    }
    //右键属性
    public function wfatt()
    {
        $info = $this->work->ProcessApi('ProcessAttView',input('id'));
        $this->assign('op',$info['show']);
        $this->assign('one',$info['info']);
        $this->assign('from',$info['from']);
        $this->assign('process_to_list',$info['process_to_list']);
        $this->assign('child_flow_list',$info['child_flow_list']);
        return $this->fetch();
    }
    public function save_attribute()
    {
        $data = input('post.');
        return json($this->work->ProcessApi('ProcessAttSave',$data['process_id'],$data));
    }

    //用户选择控件
    public function super_user()
    {
        $this->assign('user',db('auth_user')->field('uid id,true_name username')->select());
        $this->assign('kid',input('kid'));
        return $this->fetch();
    }
    //用户选择控件
    public function super_role()
    {
        $this->assign('role',db('auth_role')->field('role_id as id,role_name as username')->select());
        return $this->fetch();
    }
    public function super_get()
    {
        $type = trim(input('type'));
        if($type=='user'){
            $info =  db('auth_user')->where('true_name','like','%'.input('key').'%')->field('uid as vlaue,true_name as text')->select();
        }else{
            $info =  db('auth_role')->where('role_name','like','%'.input('key').'%')->field('role_id as vlaue,role_name as text')->select();
        }
        return ['data'=>$info,'code'=>1,'msg'=>'查询成功！'];
    }
    /*流程监控*/
    public function wfjk($map = [])
    {
        $this->assign('list', $this->work->worklist());
        return $this->fetch();
    }
    public function btn($wf_fid,$wf_type,$status)
    {
        $wf_title = ['subject_matter_invoice' => '开票', 'subject_matter_performance' => '业务提成'];
        $url = url("/admin/wf/wfcheck/",["wf_type"=>$wf_type,"wf_title"=>$wf_title[$wf_type],'wf_fid'=>$wf_fid]);
        $url_star = url("/admin/wf/wfstart/",["wf_type"=>$wf_type,"wf_title"=>$wf_title[$wf_type],'wf_fid'=>$wf_fid]);
        switch ($status)
        {
            case 0:
                return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"'.$url_star.'","450","350")>发起工作流</span>';
                break;
            case 1:
                $st = 0;
                $user_name ='';
                $flowinfo =  $this->work->workflowInfo($wf_fid,$wf_type,['uid'=>$this->uid,'role'=>$this->role]);
                if($flowinfo!=-1){
                    if(!isset($flowinfo['status'])){
                        return '<span class="btn btn-danger  radius size-S" onclick=javascript:alert("提示：当前流程故障，请联系管理员重置流程！")>Info:Flow Err</span>';
                    }
                    if($flowinfo['sing_st']==0){
                        $user = explode(",", $flowinfo['status']['sponsor_ids']);
                        $user_name =$flowinfo['status']['sponsor_text'];
                        if($flowinfo['status']['auto_person']==3||$flowinfo['status']['auto_person']==4||$flowinfo['status']['auto_person']==6){
                            if (in_array($this->uid, $user)) {
                                $st = 1;
                            }
                        }
                        if($flowinfo['status']['auto_person']==5){
                            if (in_array($this->role, $user)) {
                                $st = 1;
                            }
                        }
                    }else{
                        if($flowinfo['sing_info']['uid']==$this->uid){
                            $st = 1;
                        }else{
                            $user_name =$flowinfo['sing_info']['uid'];
                        }
                    }
                }else{
                    return '<span class="btn  radius size-S">无权限</span>';
                }
                if($st == 1){
                    return '<span class="btn  radius size-S" onclick=layer_show(\'审核\',"'.$url.'","850","650")>审核('.$user_name.')</span>';
                }else{
                    return '<span class="btn  radius size-S">无权限('.$user_name.')</span>';
                }

            case 100:
                echo '<span class="btn btn-primary" onclick=layer_show(\'代审\',"'.$url.'?sup=1","850","650")>代审</span>';
                break;
                break;
            default:
                return '';
        }
    }
    public function status($status)
    {
        switch ($status)
        {
            case 0:
                return '<span class="label radius">保存中</span>';
                break;
            case 1:
                return '<span class="label radius" >流程中</span>';
                break;
            case 2:
                return '<span class="label label-success radius" >审核通过</span>';
                break;
            default: //-1
                return '<span class="label label-danger radius" >退回修改</span>';
        }
    }

    /*发起流程，选择工作流*/
    public function wfstart()
    {
        $info = ['wf_type'=>input('wf_type'),'wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid')];
        $flow =  $this->work->getWorkFlow(input('wf_type'));
        $this->assign('flow',$flow);
        $this->assign('info',$info);
        return $this->fetch();
    }
    /*正式发起工作流*/
    public function statr_save()
    {
        $data = $this->request->param();
        $flow = $this->work->startworkflow($data,$this->uid);
        if($flow['code']==1){
            return $this->msg_return('Success!');
        }
    }

    public function wfcheck()
    {
        $info = ['wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid'),'wf_type'=>input('wf_type')];

        $this->assign('info',$info);
        $this->assign('flowinfo',$this->work->workflowInfo(input('wf_fid'),input('wf_type'),['uid'=>$this->uid,'role'=>$this->role]));
        return $this->fetch();
    }
    public function do_check_save()
    {
        $data = $this->request->param();

        $flowinfo =  $this->work->workdoaction($data,$this->uid);
        if($flowinfo['code']=='0'){
            return $this->msg_return('Success!');
        }else{
            return $this->msg_return($flowinfo['msg'],1);
        }
    }
    public function ajax_back()
    {
        $flowinfo =  $this->work->getprocessinfo(input('back_id'),input('run_id'));
        return $flowinfo;
    }
    public function Checkflow($fid){
        return $this->work->SuperApi('CheckFlow',$fid);
    }

    public function wfup()
    {
        return $this->fetch();
    }

    public function wfend()
    {
        $flowinfo =  $this->work->SuperApi('WfEnd',input('get.id'),$this->uid);
        return $this->msg_return('Success!');
    }
    public function wfupsave()
    {
        $files = $this->request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = \Env::get('root_path') . '/public/uploads/';
            $info = $file->move($path);
            if ($info) {
                $data[] = $info->getSaveName();
            } else {
                $error[] = $file->getError();
            }
        }
        return $this->msg_return($data,0,$info->getInfo('name'));
    }
    public function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
    {
        $ret = ["code" => $code, "msg" => $msg, "data" => $data];
        $extend['opt'] = [
            'alert'    => $alert,
            'close'    => $close,
            'redirect' => $redirect,
            'url'      => $url,
        ];
        $ret = array_merge($ret, $extend);
        return json($ret);
    }
    public function wflogs($id,$wf_type,$type='html'){
        $logs = $this->work->FlowLog('logs',$id,$wf_type);
        echo $logs[$type];
    }
    public function wfgl(){
        return $this->fetch($this->Tmp.'wfgl.html');
    }

    /**
     * 返回流程步骤
     *
     * @return false|string
     */
    public function getWorkflowStep()
    {
        $info = ['wf_title' => input('wf_title'), 'wf_fid' => input('wf_fid'), 'wf_type' => input('wf_type')];
        $flowinfo = $this->work->wfInfo(input('wf_fid'), input('wf_type'), ['uid' => $this->uid, 'role' => $this->role]);

        if (empty($flowinfo['log'])){
            return [
                'code'=>201,
                'data'=>[],
                'msg'=>'暂无流程'
            ];
        }
        $desc = [
            'Send'  =>'发起申请',
            'ok'    =>'审核通过',
            'SupEnd'=>'终止',
            'Send1'=>'代审',
        ];
        foreach ($flowinfo['log'] as &$item){
            $item['dateline'] = date('Y-m-d H:i:s',$item['dateline']);
            if(isset($desc[$item['btn']])){
                $item['btn'] = $desc[$item['btn']];
            }
        }
        unset($item);
        return [
            'code'=>200,
            'data'=>$flowinfo['log']
        ];
    }
}