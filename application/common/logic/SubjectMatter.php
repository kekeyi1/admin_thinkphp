<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/12
 * Time: 10:08
 */

namespace app\common\logic;

use app\common\service\Common;
use app\common\service\SubjectMatterPropertyType;

class SubjectMatter
{
    /**
     * 格式化数据 同步勘验模板数据
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public static function formatData(array $data, int $id)
    {
        foreach ($data as &$value) {
            $value['subject_matter_id'] = intval($id);
        }
        unset($value);

        return $data;
    }

    /**
     * 比较数组不同
     *
     * @param array $array1
     * @param array $array2
     * @param int $id
     * @return array
     */
    public static function diff(array $array1, array $array2, int $id)
    {
        if (empty($array1) || empty($array2)) return [];
        $array = [];
        foreach ($array1 as $item) {
            if (!in_array($item['field_id'], $array2)) {
                $item['subject_matter_id'] = $id;
                $array[]                   = $item;
            }
        }
        return $array;
    }

    /**
     * 处理财产类型字符串
     *
     * @param $string
     * @return array|string
     */
    public static function formatPropertyType($string)
    {
        if (empty($string)) return '';

        return explode(',', $string);
    }

    /**
     * 格式化参数
     *
     * @param $data
     * @param $state
     * @return array
     */
    public static function formatParam($data, $state)
    {
        $search = $param = [];
        //时间范围
        if (empty($data['create_time'])) {
            $param['create_time']  = Common::getTime();
            $search['create_time'] = '';
        } else {
            $create_time = explode(' - ', $data['create_time']);
            $param['create_time']  = [strtotime(current($create_time)), strtotime(end($create_time))];
            $search['create_time'] = $data['create_time'];
        }
        $default = [];
        //财产类型
        $property_type_first = $property_type_second = [];
        if (!empty($data['property_type'])) {
            $property_type = explode(',', $data['property_type']);
            $default = $property_type;
            if (count($property_type) == 1) {
                list($property_type_first[], $property_type_second[]) = explode('/', current($property_type));
            } else {
                foreach ($property_type as $item) {
                    list($property_type_first[], $property_type_second[]) = explode('/', $item);
                }
            }
            $search['property_type'] = $data['property_type'];
        }else{
            $property_type_first = SubjectMatterPropertyType::getPropertyTypeByLevel(true);
            $property_type_second = SubjectMatterPropertyType::getPropertyTypeByLevel(false);
        }
        //案件状态
        $search['state'] = '';
        $param['state'] = array_column($state, 'id');
        if (!empty($data['state'])) {
            $param['state']  = [$data['state']];
            $search['state'] = $data['state'];
        }

        //执行案号、创建人、承办法官、标的物名称、法院名称
        $search['value'] = '';
        $param['value'] = '';
        if (!empty($data['value'])) {
            $param['value'] = trim($data['value']);
            $search['value'] = trim($data['value']);
        }

        return [$search, $param, $default, $property_type_first, $property_type_second];
    }

    /**
     * 格式化导出数据
     *
     * @param $array1
     * @param $array2
     * @param $array3
     * @return mixed
     */
    public static function formatExportData($array1, $array2, $array3)
    {
        foreach ($array1 as $key=>&$item) {
            if (empty($item)) continue;
            $item['id'] = bcadd($key, 1);
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            $item['subject_matter_number'] = ' ' . $item['subject_matter_number'];
            if (isset($array2[$item['auction_state']])) {
                $item['auction_state'] = $array2[$item['auction_state']];
            }
            if (isset($array3[$item['property_type_first']])) {
                $item['property_type_first'] = $array3[$item['property_type_first']];
            }
            if (isset($array3[$item['property_type_second']])) {
                $item['property_type_second'] = $array3[$item['property_type_second']];
            }
        }
        unset($item);
        return $array1;
    }
}