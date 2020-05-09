<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/3
 * Time: 18:22
 */

namespace app\common\logic;


class SubjectMatterInquestTemplate
{
    /**
     * 格式化数据
     *
     * @param $data
     * @param $property
     * @return mixed
     */
    public static function formatData($data, $property)
    {

        foreach ($data as &$value) {
            if (isset($property[$value['property_type_first']])) {
                $value['property_type_first'] = $property[$value['property_type_first']];
            }
            if(is_numeric($value['property_type_first'])){
                $value['property_type_first'] = '';
            }
           
        }
        unset($value);

        return $data;
    }
}