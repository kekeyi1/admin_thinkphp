<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/16
 * Time: 11:00
 */

namespace app\admin\controller;

use think\facade\Lang;
use think\facade\Request;
use app\common\service\Status;
use app\common\validate\DeptValidate;
use app\common\model\Dept as DeptModel;
use app\common\service\Dept as DeptService;

class Dept extends Admin
{
    /**
     * 获取部门列表
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = Request::param();
        $obj = new DeptModel;

        // 分页列表
        $query = $obj->field('*')->where('agency_id', $request['agency_id'])->select();
        $list = list_for_level($query);
        $data = [
            'list' => $list,
            'agency_id' => $request['agency_id'],
        ];
        return $this->fetch('index', $data);
    }

    /**
     * 新增部门
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
            $obj = new DeptModel;
            $request = Request::param();
            $request['create_uid'] = $this->uid;
            // 验证数据
            $validate = new DeptValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $exist = $obj->where(array('dept_name' => $request['dept_name'], 'pid' => $request['pid'], 'agency_id' => $request['agency_id']))->value('id');
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
        $obj = new DeptModel;
        $request = Request::param();
        // 获取父级列表
        $query = $obj->where(array("isvalid" => Status::IS_VALID, 'agency_id' => $request['agency_id']))->select();
        $list = list_for_level($query);

        $data = [
            'list' => $list,
            'agency_id' => $request['agency_id']
        ];

        return $this->fetch('create', $data);
    }

    /**
     * 编辑部门
     *
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $request['create_uid'] = $this->uid;
            // 验证数据
            $validate = new DeptValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $obj = new DeptModel;
            $exist = $obj->where(array('dept_name' => $request['dept_name'], 'pid' => $request['pid'], 'agency_id' => $request['agency_id']))->value('id');
            if (is_numeric($exist) && $exist != $request['id']) {
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

        $obj = new DeptModel;
        $request = Request::param();
        
        $info = $obj->find($request['id']);
        // 获取父级列表
        $query = $obj->where(array("isvalid" => Status::IS_VALID, 'agency_id' => $info['agency_id']))->where("id","neq",$request['id'])->select();
        $list = list_for_level($query);
        $data = [
            'list' => $list,
            'info' => $info,
        ];

        return $this->fetch('edit', $data);
    }

    /**
     * 删除部门
     *
     * @return \think\response\Json
     */
    public function remove()
    {
        $id = Request::param('id');

        // 查看该部门是否有子部门
        $child = DeptModel::where('pid', $id)->select();
        if (!empty($child->toArray())) {
            return $this->response(201, "该部门下有子部门，不能进行删除操作");
        } else {
            $des = DeptModel::destroy($id);
            if ($des !== false) {
                return $this->response(200, Lang::get('Success del'));
            } else {
                return $this->response(201, Lang::get('Fail del'));
            }
        }
    }

    /**
     * 根据公司ID返回部门列表
     *
     * @return \think\response\Json
     */
    public function getDeptList()
    {
        $request = Request::param('id');
        if (empty($request)) return $this->response(200, Lang::get('Success'));
        $deptList = DeptService::getDeptList($request);
        $dept_list = list_for_level($deptList);

        return $this->response(200, Lang::get('Success'), $dept_list);
    }
    
     /**
     * 状态更新
     *
     * @return \think\response\Json
     */
    public function state()
    {
        $id = Request::param('id');
        $obj = new DeptModel;
        //查看当前状态情况
        $status = $obj->where(array('id' => $id))->value('isvalid'); 
        $status = !$status;
        $obj->allowField(true)->save(['isvalid' => $status],['id' => $id]);
        if (isset($obj->isvalid)) {
            return $this->response(200, Lang::get('Success edit'));  
        } else {
            return $this->response(201, Lang::get('Fail edit'));
        }
    }
}