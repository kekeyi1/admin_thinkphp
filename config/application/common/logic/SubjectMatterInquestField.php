<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/1/3
 * Time: 18:22
 */

namespace app\common\logic;


class SubjectMatterInquestField
{
    /**
     * 格式化数据
     *
     * @param $data
     * @param $fieldType
     * @param $widgetType
     * @param $formatType
     * @return mixed
     */
    public static function formatData($data, $fieldType, $widgetType, $formatType)
    {

        foreach ($data as &$value) {
            if (isset($fieldType[$value['field_type']])) {
                $value['field_type'] = $fieldType[$value['field_type']];
            }
            if (isset($widgetType[$value['widget_type']])) {
                $value['widget_type'] = $widgetType[$value['widget_type']];
            }
            if (isset($formatType[$value['field_format']])) {
                $value['field_format'] = $formatType[$value['field_format']];
            }
        }
        unset($value);

        return $data;
    }
}