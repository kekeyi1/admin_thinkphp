<?php
/* *
 * User: yangyujuan
 * Date: 2019/12/17
 * Time: 14:00
 */

namespace app\admin\controller;

use think\Db;
use think\Exception;
use think\facade\Lang;
use think\facade\Request;
use app\common\service\Agency;
use app\common\service\Status;
use app\common\service\AuthUser;
use app\common\validate\UserTeamValidate;
use app\common\model\UserTeam as TeamModel;
use app\common\logic\Team as TeamLogic;
use app\common\model\UserTeamRelation as UserTeamRelationModel;

class Team extends Admin
{
    /**
     * 获取团队列表
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = Request::param();
        $obj = new TeamModel;

        // 查询条件
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
        $uid = $this->uid;
        $list = $obj
            ->alias("a")
            ->where("a.id", "in", function ($query) use ($uid) {
                $query->table('user_team_relation')->where(['user_id' => $uid])->whereOr(['create_uid' => $uid])->field('team_id');
            })
            ->where('a.name', 'like', '%' . $q . '%')
            ->join('region b', 'b.id = a.province_id', 'left')
            ->join('region c', 'c.id = a.city_id', 'left')
            ->join('region d', 'd.id = a.area_id', 'left')
            ->join('agency e', 'e.id = a.agency_id', 'left')
            ->join('user_team_relation f', 'f.team_id = a.id and f.type=1', 'left')
            ->join('user_team_relation g', 'g.team_id = a.id and g.type=2', 'left')
            ->join('user_team_relation h', 'h.team_id = a.id and h.type=3', 'left')
            ->join('user_team_relation i', 'i.team_id = a.id and i.type=4', 'left')
            ->group("a.id")
            ->order('a.id desc')
            ->field("a.*, b.name as province_name, c.name as city_name, d.name as area_name, e.name as agency_name, group_concat(distinct f.username) as manager, group_concat(distinct g.username) as leader, group_concat(distinct h.username) as out_job, group_concat(distinct i.username) as in_job")
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
                'query' => $params,
            ]);

        $data = [
            'search' => $search,
            'list' => $list,
            'current_num' => Common::formatPage($list->currentPage(), $list->listRows()),
        ];

        return $this->fetch('index', $data);
    }

    /**
     * 新增团队
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
            $validate = new UserTeamValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $obj = new TeamModel;
            $exist = $obj->where('name', $request['name'])->value('id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }
            $court_exists = $obj->where(array('agency_id' => $request['agency_id'], 'isvalid' => 1) )->value('id');
            if (is_numeric($court_exists)) {
                return $this->response(201, Lang::get('The court already has a team'));
            }
            $court = Agency::getCourtInfo($request['agency_id']);
            list(, , $request['province_id'], $request['city_id'], $request['area_id']) = array_values($court);
            $request['create_uid'] = $this->uid;
            $request['isvalid'] = Status::IS_VALID;
            if (!empty($request['out_job'])) $request['out_job'] = explode(',', $request['out_job']);
            if (!empty($request['in_job'])) $request['in_job'] = explode(',', $request['in_job']);
            // 启动事务
            Db::startTrans();
            try {
                // 写入团队表
                $obj->allowField(true)->save($request);
                if (is_numeric($obj->id)) {
                    // 用户团队关联表数据
                    $list = [];
                    if (!empty($request['manager'])) {
                        $list[] = TeamLogic::formatData($request['manager'], AuthUser::getUserInfo($request['manager'])['true_name'], $obj->id, Status::MANAGER, Status::LEADER, $this->uid);
                    }
                    if (!empty($request['leader'])) {
                        $list[] = TeamLogic::formatData($request['leader'], AuthUser::getUserInfo($request['leader'])['true_name'], $obj->id, Status::SUPERVISOR, Status::LEADER, $this->uid);
                    }
                    if (!empty($request['out_job'])) {
                        foreach ($request['out_job'] as $v) {
                            $list[] = TeamLogic::formatData($v, AuthUser::getUserInfo($v)['true_name'], $obj->id, Status::SECRETARY, Status::NOT_LEADER, $this->uid);
                        }
                    }
                    if (!empty($request['in_job'])) {
                        foreach ($request['in_job'] as $v) {
                            $list[] = TeamLogic::formatData($v, AuthUser::getUserInfo($v)['true_name'], $obj->id, Status::LEGWORK, Status::NOT_LEADER, $this->uid);
                        }
                    }
                    // 写入
                    $relationObj = new UserTeamRelationModel;
                    foreach ($list as $data) {
                        $relationObj->data($data, true)->isUpdate(false)->save();
                    }
                    // 提交事务
                    Db::commit();
                    if (is_numeric($obj->id) && is_numeric($relationObj->id)) {
                        return $this->response(200, Lang::get('Success add'));
                    } else {
                        return $this->response(201, Lang::get('Fail add'));
                    }
                }
            } catch (Exception $ex) {
                // 回滚事务
                Db::rollback();
                return $this->response(201, Lang::get('Fail add'));
            }
        }

        // 法院
        $court = Agency::getAgencyList(Status::COURT);
        // 用户
        $user = Db::name('auth_user')->field("uid,username,true_name")->where('status',0)->select();
        // 表单token
        $token = $this->request->token('__token__', 'sha1');
        $data = [
            'court' => $court,
            'token' => $token,
            'user'  => $user,
        ];
        return $this->fetch('create', $data);
    }

    /**
     * 管理团队
     *
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function manage()
    {
        $obj = new TeamModel;
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new UserTeamValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $exist = $obj->where('name', $request['name'])->value('id');
            if (is_numeric($exist) && $exist != $request['id']) {
                return $this->response(201, Lang::get('This record already exists'));
            }
            $court_exists = $obj->where(array('agency_id' => $request['agency_id'], 'isvalid' => 1) )->value('id');
            if (is_numeric($court_exists) && $court_exists != $request['id']) {
                return $this->response(201, Lang::get('The court already has a team'));
            }
            $court = Agency::getCourtInfo($request['agency_id']);
            list(, , $request['province_id'], $request['city_id'], $request['area_id']) = array_values($court);
            if (!empty($request['out_job'])) $request['out_job'] = explode(',', $request['out_job']);
            if (!empty($request['in_job'])) $request['in_job'] = explode(',', $request['in_job']);
            // 启动事务
            Db::startTrans();
            try {
                $relationObj = new UserTeamRelationModel;
                // 写入
                $obj->isUpdate(true)->allowField(true)->save($request);
                $relationObj->where(['team_id' => $request['id']])->delete();
                if (is_numeric($obj->id)) {
                    // 用户团队关联表数据
                    $list = [];
                    if (!empty($request['manager'])) {
                        $list[] = TeamLogic::formatData($request['manager'], AuthUser::getUserInfo($request['manager'])['true_name'], $obj->id, Status::MANAGER, Status::LEADER, $this->uid);
                    }
                    if (!empty($request['leader'])) {
                        $list[] = TeamLogic::formatData($request['leader'], AuthUser::getUserInfo($request['leader'])['true_name'], $obj->id, Status::SUPERVISOR, Status::LEADER, $this->uid);
                    }
                    if (!empty($request['out_job'])) {
                        foreach ($request['out_job'] as $v) {
                            $list[] = TeamLogic::formatData($v, AuthUser::getUserInfo($v)['true_name'], $obj->id, Status::SECRETARY, Status::NOT_LEADER, $this->uid);
                        }
                    }
                    if (!empty($request['in_job'])) {
                        foreach ($request['in_job'] as $v) {
                            $list[] = TeamLogic::formatData($v, AuthUser::getUserInfo($v)['true_name'], $obj->id, Status::LEGWORK, Status::NOT_LEADER, $this->uid);
                        }
                    }
                    // 写入
                    foreach ($list as $data) {
                        $relationObj->data($data, true)->isUpdate(false)->save();
                    }
                    // 提交事务
                    Db::commit();
                    if (is_numeric($obj->id) && is_numeric($relationObj->id)) {
                        return $this->response(200, Lang::get('Success edit'));
                    } else {
                        return $this->response(201, Lang::get('Fail edit'));
                    }
                }
            } catch (Exception $ex) {
                // 回滚事务
                Db::rollback();
                return $this->response(201, Lang::get('Fail edit'));
            }
        }
        $request_id = Request::param('id');
        $info = $obj->find($request_id);
        $data["manager"] = Db::name('user_team_relation')->where(['team_id' => $request_id, 'type' => Status::MANAGER])->value("user_id");
        $data["leader"]  = Db::name('user_team_relation')->where(['team_id' => $request_id, 'type' => Status::SUPERVISOR])->value("user_id");
        $data["out_job"] = Db::name('user_team_relation')->where(['team_id' => $request_id, 'type' => Status::SECRETARY])->column('user_id');
        $data["in_job"]  = Db::name('user_team_relation')->where(['team_id' => $request_id, 'type' => Status::LEGWORK])->column('user_id');
        // 法院
        $court = Agency::getAgencyList(Status::COURT);
        // 表单token
        $token = $this->request->token('__token__', 'sha1');
        // 用户
        $user = Db::name('auth_user')->field("uid,username,true_name")->where('status',0)->select();
        $data = [
            'court' => $court,
            'token' => $token,
            'user'  => $user,
            'info'  => $info,
            'data'  => $data,
        ];
        return $this->fetch('manage', $data);
    }
}