<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/11
 * Time: 10:19
 */

namespace app\admin\controller;

use think\facade\Request;

class Error extends Admin
{
    /**
     * 空控制器重定向到首页
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        //重定向到首页
        $this->redirect('Index/index');
    }
}