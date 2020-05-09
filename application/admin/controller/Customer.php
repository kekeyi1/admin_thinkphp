<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/2
 * Time: 16:28
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\service\Status;
use app\common\service\AuthUser;
use app\common\validate\CustomerValidate;
use app\common\model\Customer as CustomerModel;
use app\common\logic\Customer as CustomerLogic;

class Customer extends Admin
{
    /**
     * 查询客户数据列表
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //获取参数
        $request = Request::param();
        list($search, $param) = CustomerLogic::formatParam($request, $this->uid);
        $keyword = $param['c.value'];
        unset($param['c.value']);

        //根据条件查询数据
        $list = Db::name('customer')
            ->alias('c')
            ->join('customer_sm_relation csr', 'c.id = csr.customer_id', 'left')
            ->join('subject_matter sm', 'sm.id = csr.subject_matter_id', 'left')
            ->whereLike('c.name|c.phone|c.address', '%' . $keyword . '%', 'and')
            ->field('c.*')
            ->where($param)
            ->group('c.id')
            ->order('c.id desc')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
                'query' => $search,
            ]);

        //返回结果集
        $data = [
            'customer'    => $list,
            'search'      => $search,
            'user'        => AuthUser::getList(),
            'sex'         => CustomerLogic::$sex,
            'data_range'  => Status::$customer_data_range,
            'state'       => CustomerLogic::$customer_state,
            'product'     => CustomerLogic::$intention_product,
            'current_num' => Common::formatPage($list->currentPage(), $list->listRows()),
        ];

        return $this->fetch('index', $data);
    }

    /**
     * 导出客户数据
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function export(){
        //获取参数
        $request = Request::param();
        list($search, $param) = CustomerLogic::formatParam($request, $this->uid);
        $sql = "select c.*,group_concat(sm.name separator ',') as sm_name
        from customer as c left join customer_sm_relation as csr on c.id = csr.customer_id left join 
        subject_matter as sm on sm.id = csr.subject_matter_id where ";
        if (isset($param['c.value'])){
            $sql.="( `c`.`name` LIKE '%{$param['c.value']}%' OR `c`.`phone` LIKE '%{$param['c.value']}%' OR `c`.`address` LIKE '%{$param['c.value']}%' ) ";
        }
        if (isset($param['c.state'])){
            $sql.="AND `c`.`state` = {$param['c.state']} ";
        }
        if (isset($param['c.create_uid'])){
            $sql.="AND `c`.`create_uid` = {$param['c.create_uid']} ";
        }
        $sql.= "group by c.id order by c.id desc";
        //根据条件查询数据
        $list = Db::query($sql);
        $list = CustomerLogic::formatData($list);

        return $this->response(200, Lang::get('Success'), $list);
    }

    /**
     * 新增客户
     *
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $request['create_uid'] = $this->uid;
            $request['time'] = time();
            $request['selected'] = empty($request['select']) ? [] : explode(',', $request['select']);
            if (empty($request['birthday'])) unset($request['birthday']);
            // 验证数据
            $validate = new CustomerValidate;
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            //客户关联标的物不得超过10条

            if (count($request['selected']) > 10) {
                return $this->response(201, Lang::get('Customer sm tips'));
            }

            $exist = CustomerModel::where('phone', $request['phone'])->value('id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            $obj = new CustomerModel();
            // 过滤post数组中的非数据表字段数据
            $obj->allowField(true)->save($request);
            $tmp = [];
            if (!empty($request['selected'])) {
                foreach ($request['selected'] as $item) {
                    $tmp[] = [
                        'customer_id'       => $obj->id,
                        'subject_matter_id' => $item,
                        'create_time'              => time(),
                    ];
                }
                $res = Db::table('customer_sm_relation')->insertAll($tmp);
            }

            if (is_numeric($obj->id)) {
                return $this->response(200, Lang::get('Success add'));
            } else {
                return $this->response(201, Lang::get('Fail add'));
            }
        }
        $token     = $this->request->token('__token__', 'sha1');
        //返回结果集
        $data = [
            'token'   => $token,
            'sex'     => CustomerLogic::$sex,
            'state'   => CustomerLogic::$customer_state,
            'product' => CustomerLogic::$intention_product,
            'user'    => AuthUser::getList(),
        ];

        return $this->fetch('create', $data);
    }

    /**
     * 编辑客户
     *
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $request['time'] = time();
            $request['sm'] = empty($request['sm']) ? '' : array_filter(explode(',', $request['sm']));
            if (empty($request['birthday'])) unset($request['birthday']);
            // 验证数据
            $validate = new CustomerValidate;
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $exist = CustomerModel::where('phone', $request['phone'])->field('id,create_uid')->find();
            //验证是否为同一人
            if ($exist['create_uid'] != $this->uid) {
                return $this->response(201, Lang::get('No permissions'));
            }
            if (!empty($exist) && is_numeric($exist['id']) && $exist['id'] != $request['id']) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            $obj = new CustomerModel();
            // 过滤post数组中的非数据表字段数据
            $obj->isUpdate(true)->allowField(true)->save($request);
            $tmp = [];
            $sm = Db::table('customer_sm_relation')->where('customer_id', $request['id'])->column('subject_matter_id');
            //判断是否有关联

            if (empty($sm)){
                //暂无关联，直接新增
                if (!empty($request['sm'])){
                    foreach ($request['sm'] as $item) {
                        $tmp[] = [
                            'customer_id'       => $request['id'],
                            'subject_matter_id' => $item,
                            'create_time'       => time(),
                        ];
                    }
                    $res = Db::table('customer_sm_relation')->insertAll($tmp);
                }
            }else{
                //如果已经有关联，判断是否为空
                if (!empty($request['sm'])) {
                    //不为空的情况，判断是否新增，是否删除
                    $diff = array_diff($sm, $request['sm']);
                    if (!empty($diff)) {
                        $res = Db::table('customer_sm_relation')
                            ->where('customer_id', $request['id'])
                            ->whereIn('subject_matter_id', $diff)
                            ->delete();
                    }
                    $diff = array_diff($request['sm'], $sm);
                    if (!empty($diff)) {
                        foreach ($diff as $item) {
                            $tmp[] = [
                                'customer_id' => $request['id'],
                                'subject_matter_id' => $item,
                                'create_time' => time(),
                            ];
                        }
                        $res = Db::table('customer_sm_relation')->insertAll($tmp);
                    }
                }else{
                    //如果返回来空值，则全部删除关联
                    Db::table('customer_sm_relation')
                        ->where('customer_id',$request['id'])
                        ->whereIn('subject_matter_id',$sm)
                        ->delete();
                }
            }

            if (is_numeric($obj->id)) {
                return $this->response(200, Lang::get('Success edit'));
            } else {
                return $this->response(201, Lang::get('Fail edit'));
            }
        }

        $obj     = new CustomerModel();
        $request = Request::param('id');
        $info    = $obj->where('id', $request)->find();
        $sm_relation = Db::table('customer_sm_relation')
            ->alias('csr')
            ->join('subject_matter sm', 'csr.subject_matter_id = sm.id')
            ->where('csr.customer_id', $request)
            ->field('sm.id as value,sm.name')
            ->select();

        $token  = $this->request->token('__token__', 'sha1');
        $data = [
            'info'    => $info,
            'sex'     => CustomerLogic::$sex,
            'state'   => CustomerLogic::$customer_state,
            'product' => CustomerLogic::$intention_product,
            'user'    => AuthUser::getList(),
            'sm'      => json_encode($sm_relation),
            'token'   => $token,
        ];

        return $this->fetch('edit', $data);
    }

    /**
     * 查看客户详情
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $obj = new CustomerModel();
        $request = Request::param('id');
        $info = $obj->where('id', $request)->find();
        $sm_relation = Db::table('customer_sm_relation')
            ->alias('csr')
            ->join('subject_matter sm', 'csr.subject_matter_id = sm.id')
            ->where('csr.customer_id', $request)
            ->field('sm.id as value,sm.name')
            ->select();
        $token = $this->request->token('__token__', 'sha1');
        $data = [
            'info'    => $info,
            'sex'     => CustomerLogic::$sex,
            'state'   => CustomerLogic::$customer_state,
            'product' => CustomerLogic::$intention_product,
            'user'    => AuthUser::getList(),
            'sm'      => json_encode($sm_relation),
            'sms'     => $sm_relation,
            'token'   => $token,
        ];

        return $this->fetch('detail', $data);
    }
}