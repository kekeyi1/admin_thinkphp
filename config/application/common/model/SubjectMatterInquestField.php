<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/9
 * Time: 15:35
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\facade\Lang;
use app\common\service\Status;

class SubjectMatterInquestField extends Model
{
    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * @var array
     */
    public $widgetType = [
        1 => 'input',
        2 => 'radio',
        3 => 'checkbox',
        4 => 'datetime',
        5 => 'select',
        6 => 'textarea',
    ];

    /**
     * @var array
     */
    public $fieldType = [
        1 => 'int',
        2 => 'tinyint',
        3 => 'smallint',
        4 => 'bigint',
        5=> 'float',
        6 => 'char',
        7 => 'varchar',
        8 => 'datetime',
        9 => 'text',
        10=> 'tinytext',
    ];

    /**
     * @var array
     */
    public $formatType = [
        1 => 'number',
        2 => 'string'
    ];

    /**
     * @param $request
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getFieldList($request)
    {
        return Db::name('subject_matter_inquest_field')
            ->where('template_id', $request['id'])
            ->whereLike('name', '%' . $request['name'] . '%')
            ->paginate(Status::PAGINATION, false, [
                'type' => '\app\common\model\page\Bootstrap',
                'var_page' => 'page',
                'query'    => $request,
            ]);
    }

    /**
     * 返回字段类型
     *
     * @return array
     */
    public function fieldType()
    {
        return $this->fieldType;
    }

    /**
     * 返回控件类型
     *
     * @return array
     */
    public function widgetType()
    {
        return $this->widgetType;
    }

    /**
     * 返回数据类型
     *
     * @return array
     */
    public function formatType()
    {
        return $this->formatType;
    }
}