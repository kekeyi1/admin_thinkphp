<?php
/**
 * PHP表单生成器
 *
 * @package  FormBuilder
 * @author   xaboy <xaboy2005@qq.com>
 * @version  2.0
 * @license  MIT
 * @link     https://github.com/xaboy/form-builder
 * @document http://php.form-create.com
 */

namespace FormBuilder;


use FormBuilder\Annotation\AnnotationReader;
use FormBuilder\Contract\ConfigInterface;
use FormBuilder\Contract\FormHandleInterface;

/**
 * 表单生成类
 *
 * Class FormHandle
 * @package FormBuilder
 */
abstract class FormHandle implements FormHandleInterface
{
    protected $action = '';

    protected $method = 'POST';

    protected $title;

    protected $formContentType;

    protected $headers = [];

    protected $fieldTitles = [];

    protected $except = [];

    protected $scene;

    /**
     * 表单 UI
     *
     * @return mixed
     */
    abstract public function ui();

    /**
     * 获取表单数据
     * @return array
     */
    protected function getFormData()
    {
        return [];
    }

    public function scene($scene = null)
    {
        if (!is_null($scene)) $this->scene = $scene;
        return $this->scene;
    }

    /**
     * 获取表单配置
     *
     * @return mixed|array|ConfigInterface
     */
    protected function getFormConfig()
    {
        return;
    }

    public function getFieldTitle($field)
    {
        return isset($this->fieldTitles[$field]) ? $this->fieldTitles[$field] : null;
    }

    /**
     * 获取表单组件
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getFormRule()
    {
        $reflectionClass = new \ReflectionClass($this);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $rule = [];
        foreach ($methods as $method) {
            $field = preg_replace('/^(.+)(Field|_field)$/', '$1', $method->name);
            $value = null;
            if ($field != $method->name && !in_array($field, $this->except)) {
                $params = $method->getParameters();
                if (isset($params[0]) && ($dep = $params[0]->getClass())) {
                    if (in_array('FormBuilder\\Contract\\FormComponentInterface', $dep->getInterfaceNames())) {
                        $componentClass = $dep->getName();
                        $value = $method->invokeArgs($this, [new $componentClass($field, $this->getFieldTitle($field))]);
                    }
                }
                if (is_null($value)) $value = $method->invoke($this);
                if (!is_null($value) && (($isArray = is_array($value)) || Util::isComponent($value))) {
                    $rule[] = compact('value', 'method', 'isArray');
                }
            }
        }
        $render = new AnnotationReader($this);
        return $render->parse($rule);
    }

    /**
     * 创建表单
     *
     * @return  Form
     */
    protected function createForm()
    {
        $ui = lcfirst($this->ui());
        return call_user_func_array(['FormBuilder\\Form', $ui], $this->getParams());
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        $params = [$this->action, $this->getFormRule()];
        $config = $this->getFormConfig();
        if (is_array($config) || $config instanceof ConfigInterface)
            $params[] = $config;

        return $params;
    }

    /**
     * 获取表单
     *
     * @return Form
     */
    public function form()
    {
        if ($this->scene && method_exists($this, $this->scene . 'Scene'))
            $this->{$this->scene . 'Scene'}();

        $form = $this->createForm()->setMethod($this->method);
        if (!is_null($this->title)) $form->setTitle($this->title)->headers($this->headers);
        $formData = $this->getFormData();
        if (is_array($formData)) $form->formData($formData);
        if ($this->formContentType) $form->setFormContentType($this->formContentType);
        $config = $this->getFormConfig();
        if ($config) $form->setConfig($config);
        return $form;
    }

    /**
     * @return string
     */
    public function view()
    {
        return $this->form()->view();
    }
}