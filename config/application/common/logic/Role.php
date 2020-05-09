<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/17
 * Time: 9:15
 */

namespace app\common\logic;

use app\common\model\Hooks;

class Role
{
    /**
     * 无限极角色权限分类
     *
     * @param $info
     * @param $array
     * @return array
     */
    public static function formatRole($info, $array)
    {
        $new = $rule_ids = [];
        if (!empty($info['rule_ids'])) {
            $rule_ids = explode(',', $info['rule_ids']);
        };

        //组装返回数据格式
        foreach ($array as $item) {
            $checked = false;
            if (in_array($item['id'], $rule_ids)) {
                $checked = true;
            }
            $new[] = [
                'title' => $item['name'],
                'id' => $item['id'],
                'field' => 'role[]',
                'checked' => $checked,
                'spread' => true,
                'pid' => $item['pid']
            ];
        }
        if (empty($new)) return [];
        $res = static::formatTree($new);
        $res = static::formatData($res);
        return $res;
    }

    /**
     * 无限极分类
     *
     * @param $array
     * @param int $pid
     * @return array
     */
    public static function formatTree($array, $pid = 0)
    {
        $res = $tem = [];
        foreach ($array as $item) {
            if ($item['pid'] == $pid) {
                //判断是否存在子数组
                $tem = self::formatTree($array, $item['id']);
                $tem && $item['children'] = $tem;
                $res[] = $item;
            }
        }
        return $res;
    }

    /**
     * 格式化数据，组件复选框显示问题
     *
     * @param $data
     * @return mixed
     */
    public static function formatData($data)
    {
        foreach ($data as &$item) {
            if (isset($item['children'])) {
                foreach ($item['children'] as &$value) {
                    if (isset($value['children'])) {
                        $checked = array_unique(array_column($value['children'], 'checked'));
                        if (count($checked) > 1) {
                            $value['checked'] = false;
                        }
                        if (count($checked) == 1) {
                            if (in_array(false, $checked)) {
                                $value['checked'] = false;
                            }
                        }
                        if (!count($checked)) {
                            $value['checked'] = false;
                        }

                        foreach ($value['children'] as &$child) {
                            $child['spread'] = false;
                        }
                    } else {
                        $value['spread'] = false;
                    }
                }
                //父级需要根据自己然后去判断
                $check = array_unique(array_column($item['children'], 'checked'));
                if (count($check) > 1) {
                    $item['checked'] = false;
                }
                if (count($check) == 1) {
                    if (in_array(false, $check)) {
                        $item['checked'] = false;
                    }
                }
                if (!count($check)) {
                    $item['checked'] = false;
                }
            } else {
                $item['spread'] = false;
            }
        }
        unset($item);
        unset($value);
        unset($child);

        return $data;
    }
}