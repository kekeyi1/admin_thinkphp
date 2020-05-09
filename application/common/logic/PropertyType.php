<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/17
 * Time: 15:02
 */

namespace app\common\logic;

class PropertyType
{
    public static function formatData($list,$q)
    {
        $arr = array();
        $result = array();
        foreach ($list as $key => $value) {
            if(strstr($value['name'], $q) !== false){
                array_push($arr, $key);
            }
        }
        foreach ($arr as $key => $value) {
            if(array_key_exists($value,$list)){
                array_push($result, $list[$value]);
            }
        }
        return $result; 
    }
}