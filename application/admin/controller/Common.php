<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/13
 * Time: 11:42
 */

namespace app\admin\controller;

use phpDocumentor\Reflection\Types\Object_;
use think\Db;
use think\facade\Lang;
use think\facade\Request;

class Common extends Admin
{
    /**
     *  更新token
     *
     * @return \think\response\Json
     */
    public function refreshToken()
    {
        $token = $this->request->token('__token__', 'sha1');
        return $this->rtResponse(200, Lang::get('Success'), $token);
    }

    /**
     * ajax数据返回，规范格式
     */
    function msg_return($msg = "操作成功！", $code = 0, $data = [], $redirect = 'parent', $alert = '', $close = false, $url = '')
    {
        $ret = ["code" => $code, "msg" => $msg, "data" => $data];
        $extend['opt'] = [
            'alert' => $alert,
            'close' => $close,
            'redirect' => $redirect,
            'url' => $url,
        ];
        $ret = array_merge($ret, $extend);
        return Response::create($ret, 'json');
    }

    function ids_parse($str, $dot_tmp = ',')
    {
        if (!$str) return '';
        if (is_array($str)) {
            $idarr = $str;
        } else {
            $idarr = explode(',', $str);
        }
        $idarr = array_unique($idarr);
        $dot = '';
        $idstr = '';
        foreach ($idarr as $id) {
            $id = intval($id);
            if ($id > 0) {
                $idstr .= $dot . $id;
                $dot = $dot_tmp;
            }
        }
        if (!$idstr) $idstr = 0;
        return $idstr;
    }

    /**
     * 比较键值，并返回交集
     */
    public static function isExist($data)
    {
        //超级管理员、测试人员、财务、法院管理员
        $role_ids = [1, 14, 8, 23, 4];
        $data = explode(',', $data);
        $res = array_intersect($data, $role_ids);
        return $res;
    }

    /**
     * 模糊匹配标的
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSmList()
    {
        $request = Request::param();
        $list = Db::table('case_list')
            ->alias('al')
            ->join('subject_matter sm', 'al.id=sm.case_id')
            ->whereLike('sm.name', '%' . $request['keyword'] . '%')
            ->field('sm.id as value,sm.name')
            ->order('sm.id desc')
            ->limit(10)
            ->select();

        return $this->response(0, 'success', $list);
    }

    /**
     * 返回当前页数的数据数量
     *
     * @param $current_page
     * @param $list_rows
     * @return int
     */
    public static function formatPage($current_page, $list_rows)
    {
        $current_page = bcsub($current_page, 1);
        $num = bcmul($current_page, $list_rows);

        return intval($num);
    }
}
