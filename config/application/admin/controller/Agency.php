<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/11
 * Time: 11:00
 */

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\service\Status;
use app\common\validate\AgencyValidate;
use app\common\model\Agency as AgencyModel;

class Agency extends Admin
{
    /**
     * 获取公司列表
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = Request::param();
        $obj = new AgencyModel;
        
        // 查询条件
        $params = [];
        $search = [];
        $where = [];
        if (isset($request['q'])) {
            $q = $request['q'];
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
        } else {
            $q = '';
            $search['q'] = '';
        } 
       if (isset($request['isvalid'])) {
            if ($request['isvalid']==='0' || $request['isvalid']==1) {
                $where['isvalid'] = $request['isvalid'];
                $params['isvalid'] = $request['isvalid'];
                $search['isvalid'] = $request['isvalid'];
            }else{
                $search['isvalid'] = '';
                $params['isvalid'] = '';
            }
        }else{
                $search['isvalid'] = '';
                $params['isvalid'] = '';
        }
        // 分页列表
        $list = $obj
            ->field('*')
            ->where($where)
            ->where('name', 'like', '%' . $q . '%')
            ->order('id desc')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
                'query' => $params,
            ]);
        $type = $obj->getType();
        $data = [
            'search' => $search,
            'list' => $list,
            'type' => $type,
        ];
        return $this->fetch('index', $data);
    }

    /**
     * 新增公司
     *
     * @return mixed|\think\response\Json
     */
    public function create()
    {
        $obj = new AgencyModel;

        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $request['create_uid'] = $this->uid;
            // 验证数据
            $validate = new AgencyValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $exist = $obj->where('name', $request['name'])->value('id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            // 写入
            $obj->allowField(true)->save($request);
            if (is_numeric($obj->id)) {
                return $this->response(200, Lang::get('Success add'));
            } else {
                return $this->response(201, Lang::get('Fail add'));
            }
        }
        $type = $obj->getType();
        $data = ['list' => $type,];
        return $this->fetch('create', $data);
    }

    /**
     * 编辑公司
     *
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        $obj = new AgencyModel;

        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new AgencyValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $exist = $obj->where('name', $request['name'])->value('id');
            if (is_numeric($exist) && $request['id'] != $exist) {
                return $this->response(201, Lang::get('This record already exists'));
            }
            // 写入
            $obj->isUpdate(true)->allowField(true)->save($request);
            if (is_numeric($obj->id)) {
                return $this->response(200, Lang::get('Success edit'));
            } else {
                return $this->response(201, Lang::get('Fail edit'));
            }
        }

        $request_id = Request::param('id');
        $info = $obj->find($request_id);
        $type = $obj->getType();
        $data = [
            'info' => $info,
            'list' => $type
        ];
        return $this->fetch('edit', $data);
    }
}