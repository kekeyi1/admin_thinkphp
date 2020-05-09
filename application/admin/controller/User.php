<?php

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\model\Agency;
use app\common\model\AuthUser;
use app\common\model\AuthRole;
use app\common\service\Status;
use app\common\validate\UserValidate;
use app\common\service\UserPosition;
use app\common\service\Dept as DeptService;

class User extends Admin
{
    /**
     * 获取用户列表
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = Request::param();

        $params = [];
        $search = [];

        if (isset($request['q'])) {
            $q = $request['q'];
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
        } else {
            $q = '';
            $search['q'] = '';
        }

        $userObj = new AuthUser;
        $list = $userObj
            ->alias("u")
            ->join("dept d","d.id=u.dept_id","left")
            ->join("agency a","a.id=u.agency_id","left")    
            ->field('u.*, d.dept_name, a.name as agency_name')
            ->whereOr('u.username|u.phone|u.email|d.dept_name|a.name|u.true_name', 'like', '%' . $q . '%')
            ->order('u.uid desc')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
                'query' => $params,
            ]);

        $new_list = [];
        $roleObj = new AuthRole;
        if (isset($list)) {
            foreach ($list as $v) {
                if (!empty($v['role_ids'])) {
                    $v['role'] = $roleObj->getAll(explode(',', $v['role_ids']));
                } else {
                    $v['role'] = '';
                }
                array_push($new_list, $v);
            }
        }

        $data = [
            'search'      => $search,
            'list'        => $list,
            'page'        => $list->render(),
            'current_num' => Common::formatPage($list->currentPage(), $list->listRows()),
        ];
        return $this->fetch('index', $data);
    }

    /**
     * 创建用户
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
            // 验证数据
            $validate = new UserValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $exist = AuthUser::where('username', $request['username'])->value('uid');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            $userObj = new AuthUser;
            $request['password'] = password_hash($request['password'], PASSWORD_DEFAULT);
            $request['role_ids'] = $request['ids'];
            $userObj->allowField(true)->save($request);
            if (is_numeric($userObj->uid)) {
                return $this->response(200, Lang::get('Success add'));
            } else {
                return $this->response(201, Lang::get('Fail add'));
            }
        }

        // 角色列表
        $roleObj = new AuthRole;
        $role = $roleObj->order('sort asc')->select();
        $agencyObj = new Agency;
        $agency = $agencyObj->where('isvalid', Status::IS_VALID)->order('id asc')->select();
        $this->assign('role', $role);
        $this->assign('agency', $agency);
        $this->assign('position', UserPosition::getList());
        return $this->fetch('create');
    }

    /**
     * 编辑用户
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new UserValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            
            $userObj = new AuthUser;
            $request['role_ids'] = $request['ids'];
            $userObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($userObj->uid)) {
                return $this->response(200, Lang::get('Success edit'));
            } else {
                return $this->response(201, Lang::get('Fail edit'));
            }
        }

        // 角色列表
        $id = Request::param('id');
        $info = AuthUser::where('uid', $id)->find();

        $roleObj = new AuthRole;
        $agencyObj = new Agency;

        $list = $roleObj->order('sort asc')->select();
        $agency = $agencyObj->order('id asc')->select();
        $deptList = DeptService::getDeptList($info['agency_id']);
        $dept_list = list_for_level($deptList);
        $data = [
            'agency'    => $agency,
            'dept_list' => $dept_list,
            'info'      => $info,
            'list'      => $list,
            'position'  => UserPosition::getList()
        ];
        return $this->fetch('edit', $data);
    }

    /**
     * 编辑个人信息
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editPassport()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new UserValidate();
            $validateResult = $validate->scene('editPassport')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            // 并查询用户信息
            $userInfo = Db::name('auth_user')
            ->field('uid,username,password,status,role_ids,true_name,agency_id')
            ->where('uid', $request['uid'])
            ->find();
        
          
            // 验证密码 
            if (!password_verify($request['origin_password'], $userInfo['password'])) {
                return $this->response(201, Lang::get('Fail origin password'));
            }
            //修改后的密码不得与原始密码一致
            if (password_verify($request['password'], $userInfo['password']) || password_verify($request['repassword'], $userInfo['password'])) {
                return $this->response(201, Lang::get('Fail password again'));
            }
            $request['password'] = password_hash($request['password'], PASSWORD_DEFAULT);
            $userObj = new AuthUser;
            $userObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($userObj->uid)) {
                return $this->response(200, Lang::get('Success edit'));
            } else {
                return $this->response(201, Lang::get('Fail edit'));
            }
        }

        // 角色列表
        $id = Request::param('id');
        $info = AuthUser::where('uid', $id)->find();

        $roleObj = new AuthRole;
        $agencyObj = new Agency;

        $list = $roleObj->order('sort asc')->select();
        $agency = $agencyObj->order('id asc')->select();
        $deptList = DeptService::getDeptList($info['agency_id']);
        $dept_list = list_for_level($deptList);
        $data = [
            'agency'    => $agency,
            'dept_list' => $dept_list,
            'info'      => $info,
            'list'      => $list,
            'position'  => UserPosition::getList()
        ];
        return $this->fetch('edit_passport', $data);
    }

    /**
     * 编辑个人信息
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userInfo()
    {
        // 角色列表
        $id = Request::param('id');
        $info = AuthUser::where('uid', $id)->find();

        $roleObj = new AuthRole;
        $agencyObj = new Agency;

        $list = $roleObj->order('sort asc')->select();
        $agency = $agencyObj->order('id asc')->select();
        $deptList = DeptService::getDeptList($info['agency_id']);
        $dept_list = list_for_level($deptList);
        $data = [
            'agency'    => $agency,
            'dept_list' => $dept_list,
            'info'      => $info,
            'list'      => $list,
            'position'  => UserPosition::getList()
        ];
        return $this->fetch('user_info', $data);
    }

    /**
     * 删除用户
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $id = Request::param('id');

        $userObj = new AuthUser;
        $return = $userObj->where('uid', $id)->delete();
        if ($return !== false) {
            return $this->response(200, Lang::get('Success del'));
        } else {
            return $this->response(201, Lang::get('Fail del'));
        }
    }
    
    /**
     * 状态更新
     *
     * @return \think\response\Json
     */
    public function state()
    {
        $id = Request::param('id');
        $obj = new AuthUser;
        //查看当前状态情况
        $status = $obj->where(array('uid' => $id))->value('status'); 
        $status = !$status;
        $obj->allowField(true)->save(['status' => $status],['uid' => $id]);
        if (isset($obj->status)) {
            return $this->response(200, Lang::get('Success edit'));  
        } else {
            return $this->response(201, Lang::get('Fail edit'));
        }
    }
    

}