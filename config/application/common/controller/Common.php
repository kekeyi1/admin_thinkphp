<?php
namespace app\Common\controller;

use think\Controller;

class Common extends Controller
{
    /**
     * 返回响应数格式
     */
    protected function response($code, $msg = '', $data = [])
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'count'=> count($data) + 1,
        ];

        return json($response, 200);
    }

     /**
     * 返回响应数格式
     */
    protected function rtResponse($code, $msg = '', $data = [])
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        return json($response, 200);
    }
}