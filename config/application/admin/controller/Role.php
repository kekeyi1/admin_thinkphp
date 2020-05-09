<?php

namespace app\admin\controller;

use think\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\model\AuthRole;
use app\common\validate\RoleValidate;
use app\common\logic\Role as RoleLogic;

class Role extends Admin
{
    /**
     * 获取角色列表
     *
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $list = Db::name('auth_role')->order('sort asc,role_id asc')->select();
        return view('index', ['list' => $list]);
    }

    /**
     * 角色权限编辑
     *
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function auth()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 如果为空 管理站点全部置空
            $ids = isset($request['role']) ? implode(',', $request['role']) : '';
            $upstatus = AuthRole::where('role_id', $request['role_id'])->update(['rule_ids' => $ids]);

            if (!is_numeric($upstatus)) {
                return $this->response(201, Lang::get('Fail edit'));
            } else {
                return $this->response(200, Lang::get('Success edit'));
            }
        }

        // 当前角色信息
        $id = Request::param('id');
        $info = Db::name('auth_role')->where('role_id', $id)->find();

        // 所有权限列表
        $list = Db::name('auth_rule')->order('sort asc')->select();
        $list = RoleLogic::formatRole($info, $list);
        $data = [
            'list' => json_encode($list,true),
            'info' => $info,
        ];

        return view('auth', $data);
    }

    /**
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function siteAuth()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 如果为空 管理站点全部置空
            $ids = isset($request['ids']) ? implode(',', $request['ids']) : '';
            $upstatus = AuthRole::where('role_id', $request['role_id'])->update(['site_ids' => $ids]);

            if (!is_numeric($upstatus)) {
                return $this->response(201, Lang::get('Fail'));
            } else {
                return $this->response(200, Lang::get('Success'));
            }
        }

        // 当前角色信息
        $id = Request::param('id');
        $info = Db::name('auth_role')->where('role_id', $id)->find();

        // 所有权限列表
        $list = Db::name('site')->order('sort asc')->select();
        $site = explode(',', $info['site_ids']);

        return view('site_auth', ['info' => $info, 'list' => $list, 'site' => $site]);
    }

    /**
     * 删除角色
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $id = Request::param('id');
        $return = Db::name('auth_role')->where('role_id', $id)->delete();
        if ($return !== false) {
            return $this->response(200, Lang::get('Success del'));
        } else {
            return $this->response(201, Lang::get('Fail del'));
        }
    }

    /**
     * 编辑角色
     *
     * @return \think\response\Json|\think\response\View
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
            // 验证数据
            $validate = new RoleValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $exist = Db::name('auth_role')->where('role_name', $request['role_name'])->value('role_id');
            if (!empty($exist) && $exist != $request['role_id']) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            $return = Db::name('auth_role')->where('role_id', $request['role_id'])->update($request);
            if ($return !== false) {
                return $this->response(200, Lang::get('Success edit'));
            } else {
                return $this->response(201, Lang::get('Fail edit'));
            }
        }

        $id = Request::param('id');
        $info = Db::name('auth_role')->where('role_id', $id)->find();
        return view('edit', ['info' => $info]);
    }

    /**
     * 新增角色
     *
     * @return \think\response\Json|\think\response\View
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
            $validate = new RoleValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $exist = Db::name('auth_role')->where('role_name', $request['role_name'])->value('role_id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            $returnId = Db::name('auth_role')->insertGetId($request);
            if (is_numeric($returnId)) {
                return $this->response(200, Lang::get('Success add'));
            } else {
                return $this->response(201, Lang::get('Fail add'));
            }

        }

        $query = Db::name('auth_rule')->order('sort asc')->select();
        $list = list_for_level($query);
        return view('create', ['list' => $list]);
    }
}