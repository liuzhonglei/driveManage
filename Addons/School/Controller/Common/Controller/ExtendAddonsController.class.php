<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/12
 * Time: 19:04
 */

namespace Addons\School\Controller\Common\Controller;

use Home\Controller\AddonsController;
use Think\Model;

/**
 * 基础服务扩展
 * Class ExtendAddonsController
 * @package Addons\School\Controller\Common\Controller
 */
class ExtendAddonsController extends AddonsController
{
    /**
     * 需要修改查询的表
     */
    public static $tableNames = array('student', 'student_question', 'student_banner', 'teacher', 'student_notification', 'qingqing_coupon', 'eo2o_payment');

    /**
     * 附加要查询的数据
     * @var array
     */
    var $fields = array();

    /**
     * 模板变量赋值
     *
     * @access protected
     * @param mixed $name
     *            要显示的模板变量
     * @param mixed $value
     *            变量的值
     * @return Action
     */
    protected function assign($name, $value = '')
    {
        // set the teahcer data
        if ($name == 'fields') {
            $value = $this->getFieldList($value);
        }

        // return
        return parent::assign($name, $value);
    }


    /**
     * 获取搜索条件
     * @param $model
     * @param $fields
     * @return array|string
     */
    public function _search_map($model, $fields)
    {
        $map = '';

        foreach ($_REQUEST as $name => $value) {
            if (strpos($name, ',')) {
                $fieldName = $name;
                $fieldValue = $value;
            }
        }


        if (!empty($fieldName)) {
            $where_str = " token = \"" . get_token() . "\"  ";
            $map = parent::_search_map($model, $fields);

            unset($map['token']);
            unset($map[$fieldName]);
            // convert map
            foreach ($map as $mapMame => $mapValue) {
                $where_str = $where_str . " and " . $mapMame . ' = "' . $mapValue . '" ';
            }


            // add the high search
            if (!empty($fieldValue)) {
                $where_str = $where_str . ' and  (';
                $names = explode(',', $fieldName);
                $values = explode(' ', $fieldValue);
                foreach ($values as $fieldValue) {
                    $where_str = $where_str . ' (';
                    foreach ($names as $field) {
                        if (!empty($field) && !empty($fieldValue)) {
                            $where_str = $where_str . $field . ' like "%' . $fieldValue . '%" ' . 'or ';
                        }
                    }
                    if (strrchr($where_str, 'or ') == 'or ') {
                        $where_str = substr($where_str, 0, sizeof($where_str) - 4);
                    }
                    $where_str = $where_str . ' ) and ';
                }

                if (strrchr($where_str, 'and ') == 'and ') {
                    $where_str = substr($where_str, 0, sizeof($where_str) - 5);
                }

                $where_str = $where_str . ' ) ';
            }
            $map = $where_str;

            // get out
        }
        if (empty($map)) {
            $_SERVER["PATH_INFO"];
        }

        if (empty($map)) {
            $map = parent::_search_map($model, $fields);
        }
        return $map;
    }

    /**
     * 解析列表规则
     * @param $model
     * @return mixed
     */
    public function _list_grid($model)
    {
        $fields = $this->fields;
        $grids = preg_split('/[;\r\n]+/s', htmlspecialchars_decode($model ['list_grid']));
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val = explode(':', $value);
            // 支持多个字段显示
            $field = explode(',', $val [0]);
            $value = array(
                'field' => $field,
                'title' => $val [1]
            );
            if (preg_match('/^([0-9]*)%/', $val [1], $matches)) {
                $value ['title'] = str_replace($matches [0], '', $value ['title']);
                $value ['width'] = $matches [1];
            }
            if (strpos($value ['title'], "_no_order") > -1) {
                $value['order'] = "0";
                $value ['title'] = str_replace("_no_order", '', $value ['title']);
            }

            if (isset ($val [2])) {
                // 链接信息
                $value ['href'] = $val [2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use (&$fields) {
                    $fields [] = $match [1];
                }, $value ['href']);
            }
            if (strpos($val [1], '|')) {
                // 显示格式定义
                list ($value ['title'], $value ['format']) = explode('|', $val [1]);
            }
            foreach ($field as $val) {
                $array = explode('|', $val);
                $fields [] = $array [0];
            }
        }
        // 过滤重复和错误字段信息
        $sign = ture;
        if (!empty($this->listsTable)) {
            $sign = false;
        } else {
            foreach (self::$tableNames as $tableName) {
                if (!strcmp($tableName, $model['name'])) {
                    $sign = false;
                    break;
                }
            }
        }
        if ($sign) {
            $model_fields = M('attribute')->where('model_id=' . $model ['id'])->field('name')->select();
            $model_fields = getSubByKey($model_fields, 'name');
            in_array('id', $model_fields) || array_push($model_fields, 'id');
            // 可能存在使用视图进行查询的情况
            // $fields = array_intersect($fields, $model_fields);
        }
        $res ['fields'] = array_unique($fields);
        $res ['list_grids'] = $grids;
        return $res;
    }


    /**
     * 获取数据
     * @param null $model
     * @param int $page
     * @param string $order
     *
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = null, $map = null)
    {
        $order || $order = 'id desc';
        $page || $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $fields = $list_data ['fields'];

        // 搜索条件
        if (empty($map)) {
            $model_fields = M('attribute')->where('model_id=' . $model ['id'])->field('name')->select();
            $mapField = Array();
            foreach ($model_fields as $filed) {
                array_push($mapField, $filed['name']);
            }
            $map = $this->_search_map($model, $mapField);
        }

        // 关键字搜索
        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];

        // 读取模型数据列表
        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');

        // special handle
        if (!empty($this->listsTable)) {
            $name = $this->listsTable;
        } else {
            $name = get_table_name($model ['id']);
            foreach (self::$tableNames as $handleName) {
                if (!strcmp($handleName, $name)) {
                    $name .= '_all';
                    break;
                }
            }
        }

        //$name = parse_name ($name, true );
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order($order)->page($page, $row)->select();

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();


        // 设置数据和数据
        $list_data ['list_data'] = $data;
        $list_data ['count'] = $count;

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $list_data ['_page'] = $page->show();
        }

        return $list_data;
    }


}