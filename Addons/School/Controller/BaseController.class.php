<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

require_once 'Excel/PHPExcel.php';
require_once 'Excel/PHPExcel/Reader/Excel2007.php';
require_once 'Excel/PHPExcel/Reader/Excel5.php';
include_once 'Excel/PHPExcel/IOFactory.php';
include_once 'Excel/PHPExcel/style/Alignment.php';
include_once 'Excel/PHPExcel/style/NumberFormat.php';
require_once(dirname(__FILE__) . '/../../QingQing/Public/Weixin/JSSDK.php');

use Home\Controller\AddonsController;
use Think\Model;

define ('MOBILE_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Mobile');
define ('MOBILE_PATH', "Addons://School@Mobile/");

define ('QINGQING_TOKEN', "gh_94ecad95d624");

class BaseController extends AddonsController
{
    // param
    var $model;
    var $listsTable;
    var $dataMultiEdit = false;

    // the need show all table
    public static $tableNames = array('student', 'student_question', 'student_banner', 'teacher', 'student_notification', 'qingqing_coupon', 'eo2o_payment');

    // 通用插件的增加模型
    public function lists()
    {
        $_POST['token'] = get_token();
        parent::common_lists($this->model);
    }

    // 通用插件的增加模型
    public function add()
    {
        $_POST['token'] = get_token();
        parent::common_add($this->model);
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $_POST['token'] = get_token();
        parent::common_edit($this->model);
    }

    // 通用插件的删除模型
    public function del()
    {
        $_POST['token'] = get_token();
        parent::common_del($this->model);
    }

    /**
     * get the model grid fields
     * @return the model fields
     */
    public function getModelFields()
    {
        $model = $this->model;
        $list_data = $this->_list_grid($model);

        if ($this->dataMultiEdit) {
            array_unshift($list_data['fields'], 'select');
            array_unshift($list_data['list_grids'], array("title" => '<input type="checkbox" class="group-checkable">'));
        }

        $this->ajaxReturn($list_data);
    }

    /**
     * get the model info
     */
    public function getModelInfo()
    {
        $model = $this->model;
        $fieldList = $this->getFieldList($model);

        // result
        $result = array();
        $result['model'] = $model;
        $result['fieldList'] = $fieldList;

        // return
        $this->ajaxReturn($result);
    }

    /**
     * get the model fileds config
     * @param $model the model name
     */
    public function getFieldList($model)
    {
        // get the  fields
        $model || $model = $this->getModel($_GET['model']); // 默认显示第一页数据
        $fields = get_model_attribute($model['id']);

        // set the extra data
        for ($i = 1; $i <= count($fields); $i++) {
            for ($j = 0; $j < count($fields[$i]); $j++) {
                if (in_array($fields[$i][$j]['name'], array('id_teacher', 'id_teacher_k2', 'id_teacher_k3', 'id_in_teacher'))) {
                    if (empty($teacherData)) {
                        $teacherData = $this->getFieldData('teacher', array('status' => 1, 'token' => get_token()));
                    }
                    $fields[$i][$j] ['extra'] .= "\r\n" . $teacherData;
                } else if (in_array($fields[$i][$j]['name'], array('course_id'))) {
                    if (empty($teacherData)) {
                        $courseData = $this->getFieldData('school_course', array('token' => get_token()));
                    }
                    $fields[$i][$j] ['extra'] .= "\r\n" . $courseData;
                }
            }
        }


        // return
        return $fields;
    }


    /**
     * get the data by id
     * @param null $model
     * @param int $id
     */
    public function getDataById($model = null, $id = 0)
    {
        // param
        $token = get_token();
        $model || $model = i("model");
        $model = $this->getModel()['name'];
        $id || $id = i("id");

        // search
        $info = M($model)->where('id="' . $id . '" and token= "' . $token . '" ')->find();
        if (!empty($info["photo"])) {
            $info["photoUrl"] = get_cover($info["photo"])['path'];
        } else {
            $info["photoUrl"] = "http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image";
        }

        return $this->ajaxReturn($info);
    }

    /**
     * get the model data
     * @param null $model
     * @param int $page
     * @param string $order
     */
    public function listsAdmin()
    {
        // page and length
        $custromActionType = i("custromActionType");
        $customActionName = i("customActionName");
        $start = I('start', 0, 'intval');
        $row = I('length', 1, 'intval');
        $model = $this->model;

        // if remove some data
        if ($customActionName == "delete") {
            $_POST['ids'] = $_POST['id'];
            unset($_REQUEST['id']);
            unset($_POST['id']);
            if (!$this->commonDel($model)) {
                $this->error("数据删除失败");
            }
        }

        // page
        $page = floor($start / $row) + 1;

        // max row
        $model ['list_row'] = $row;

        // model and  fields
        $list_data = $this->_list_grid($model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];


        // order
        $orderList = i('order');
        if (!empty($orderList)) {
            $order = "";
        }
        foreach ($orderList as $orderItem) {
            if (!empty($order)) {
                $order .= ", ";
            }
            $order .= $fields[$orderItem["column"]] . " " . $orderItem["dir"];
        }

        // list data
        $list_data = $this->_get_model_list($model, $page,$order);

        // convert
        $list_data ['data'] = array();
        foreach ($list_data ['list_data'] as $record) {
            $convertRecord = array_values($record);
            for ($i = 0; $i < count($record); $i++) {
                $convertRecord[$i] = $this->get_list_field($record, $list_data ['list_grids'][$i], $model);
            }
            if ($this->dataMultiEdit) {
                array_unshift($convertRecord, '<input type="checkbox" name="id[]" value="' . $record['id'] . '">');
            }
            $list_data ['data'][] = $convertRecord;
        }

        // set others
        $list_data ['recordsTotal'] = $list_data ['count'];
        $list_data["recordsFiltered"] = $list_data ['count'];
        $list_data["draw"] = i("draw");

        // return
        $this->ajaxReturn($list_data);
    }

    /**
     * get  and set the config
     */
    public function configAdmin()
    {
        $this->getModel();
        if (IS_POST) {
            $conf = $GLOBALS['HTTP_RAW_POST_DATA'];
            $conf = json_decode($conf);
            $flag = D('Common/AddonConfig')->set(_ADDONS, $conf);

            if ($flag !== false) {
                $this->success('保存成功', Cookie('__forward__'));
            } else {
                $this->error('保存失败');
            }
        }

        $map ['name'] = _ADDONS;
        $addon = M('Addons')->where($map)->find();
        if (!$addon)
            $this->error('插件未安装');
        $addon_class = get_addon_class($addon ['name']);
        if (!class_exists($addon_class))
            trace("插件{$addon['name']}无法实例化,", 'ADDONS', 'ERR');
        $data = new $addon_class ();
        $addon ['addon_path'] = $data->addon_path;
        $addon ['custom_config'] = $data->custom_config;
        $this->meta_title = '设置插件-' . $data->info ['title'];
        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $addon ['config'] = include $data->config_file;
        if ($db_config) {
            foreach ($addon ['config'] as $key => $value) {
                if ($value ['type'] != 'group') {
                    !isset ($db_config [$key]) || $addon ['config'] [$key] ['value'] = $db_config [$key];
                } else {
                    foreach ($value ['options'] as $gourp => $options) {
                        foreach ($options ['options'] as $gkey => $value) {
                            !isset ($db_config [$key]) || $addon ['config'] [$key] ['options'] [$gourp] ['options'] [$gkey] ['value'] = $db_config [$gkey];
                        }
                    }
                }
            }
        }

        // return
        $this->ajaxReturn($addon);
    }


    /**
     * delete the data
     * @param $model model
     * @return bool is succeed
     */
    public function commonDel($model)
    {
        is_array($model) || $model = $this->getModel($model);

        !empty ($ids) || $ids = I('id');
        !empty ($ids) || $ids = array_filter(array_unique(( array )I('ids', 0)));
        !empty ($ids) || $this->error('请选择要操作的数据!');

        $Model = M(get_table_name($model ['id']));
        $map ['id'] = array(
            'in',
            $ids
        );

        // 插件里的操作自动加上Token限制
        $token = get_token();
        if (defined('ADDON_PUBLIC_PATH') && !empty ($token)) {
            $map ['token'] = $token;
        }

        if ($Model->where($map)->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * admin delete
     */
    public function modelDelete($model)
    {
        $this->ajaxReturn($this->common_del($model));
    }

    /**
     * admin save
     */
    public function  saveAdmin(){
        $this->ajaxReturn($this->saveModel());
    }


    /**
     * save the post data
     * @return bool
     */
    protected function saveModel($model = null)
    {
        //set token
        $_POST["token"] || $_POST["token"] = get_token();

        //param
        $model || $model = $_GET["model"];
        $model || $model = $this->model;
        is_array($model) || $model = $this->getModel($model);
        $id = i('id');

        // process
        $Model = D(parse_name(get_table_name($model ['id']), 1));
        $Model = $this->checkAttr($Model, $model['id']);
        if (empty(i('id'))) {
            $result = $Model->create() && $id = $Model->add();
        } else {
            $result = $Model->create() && $id = $Model->save();
        }
        if ($result || empty($Model->getError())) {
            $this->_saveKeyword($this->model, $id);
            $result = array("status" => "1", "info" => '保存成功');
        } else {
            $result = array("status" => "0", "info" => "保存失败！" . $Model->getError());
        }

        // return
        return $result;
    }

    /**
     * download the model excel
     */
    public function downloadExcel($list_data = null)
    {
        // get the data
        $model = $this->model;
        $model ['list_row'] = 100000;
        if (empty($list_data)) {
            $list_data = $this->_get_model_list($model);
        }

        foreach ($list_data['list_data'] as &$record) {
            for ($i = 0; $i < count($record); $i++) {
                $record[$list_data['fields'][$i]] = get_list_field($record, $list_data['list_grids'][$i], $model);
            }
        }

        // 创建一个处理对象实例
        $objExcel = new \PHPExcel();

        //设置文档基本属性
        $objProps = $objExcel->getProperties();
        $objProps->setCreator("QingQing");
        $objProps->setTitle("导出数据");

        //设置当前的sheet索引，用于后续的内容操作。
        //一般只有在使用多个sheet的时候才需要显示调用。
        //缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();

        //由PHPExcel根据传入内容自动判断单元格内容类型
        $skip = 0;
        if (end($list_data['fields']) == "id") {
            $skip = 1;
        }
        for ($i = 0, $b = 'A'; $i < sizeof($list_data['list_grids']) - $skip; $i++, $b++) {
            $objActSheet->setCellValue($b . "1", $list_data['list_grids'][$i]["title"]);;
            //$objActSheet->getColumnDimension($b)->setAutoSize(true);   //内容自适应
            $objActSheet->getColumnDimension($b)->setWidth(20);   //内容自适应
            $objActSheet->getStyle($b)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        }

        for ($i = 0; $i <= sizeof($list_data['list_data']); $i++) {
            for ($j = 0, $b = 'A'; $j < sizeof($list_data['list_data'][$i]) - $skip; $j++, $b++) {
                $objActSheet->setCellValueExplicit($b . ($i + 2), $list_data['list_data'][$i][$list_data['fields'][$j]],
                    \PHPExcel_Cell_DataType::TYPE_STRING);;
            }
        }

        // write to file
        $outputFileName = get_token() . i("model") . ".xls";
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
        $objWriter->save($outputFileName);

        redirect(__ROOT__ . "/" . $outputFileName);
    }


    /**
     * 获取数据
     * @param null $model
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = 'id desc')
    {
        $page || $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 搜索条件
        $map = $this->_search_map($model, $fields);

        // 关键字搜索
        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];


        // 读取模型数据列表
        if ($model ['extend']) {
            $name = get_table_name($model ['id']);
            $parent = get_table_name($model ['extend']);
            $fix = C("DB_PREFIX");

            $key = array_search('id', $fields);
            if (false === $key) {
                array_push($fields, "{$fix}{$parent}.id as id");
            } else {
                $fields [$key] = "{$fix}{$parent}.id as id";
            }

            /* 查询记录数 */
            $count = M($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->where($map)->count();

            // 查询数据
            $data = M($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->field(empty ($fields) ? true : $fields)->where($map)->order("{$fix}{$parent}.{$order}")->page($page, $row)->select();
        } else {
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
        }
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


    /**
     * 解析列表规则
     * @param $model
     * @return mixed
     */
    public function _list_grid($model)
    {
        $fields = array();
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
            $fields = array_intersect($fields, $model_fields);
        }
        $res ['fields'] = array_unique($fields);

        $res ['list_grids'] = $grids;
        return $res;
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
            // converth map
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
     * 解析列表定义规则(数据格式转换)
     * @param $data
     * @param $grid
     * @param $model
     * @return mixed|string
     */
    function get_list_field($data, $grid, $model)
    {
        // 获取当前字段数据
        foreach ($grid ['field'] as $field) {
            $array = explode('|', $field);
            $array [0] = trim($array [0]);
            $temp = $data [$array [0]];
            // 函数支持
            if (isset ($array [1])) {
                if ($array [1] == 'get_name_by_status') {
                    $temp = get_name_by_status($temp, $array [0], $model ['id']);
                } else {
                    $temp = call_user_func($array [1], $temp);
                }
            }
            $data2 [$array [0]] = $temp;
        }
        if (!empty ($grid ['format'])) {
            $value = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data2) {
                return $data2 [$match [1]];
            }, $grid ['format']);
        } else {
            $value = implode(' ', $data2);
        }

        // 链接支持
        if (!empty ($grid ['href'])) {
            $links = explode(',', $grid ['href']);
            foreach ($links as $link) {
                $array = explode('|', $link);
                $href = $array [0];
                if (preg_match('/^\[([a-z_]+)\]$/', $href, $matches)) {
                    $val [] = $data2 [$matches [1]];
                } else {
                    $show = isset ($array [1]) ? $array [1] : $value;
                    // 增加跳转方式处理 weiphp
                    $target = '_self';
                    if (preg_match('/target=(\w+)/', $href, $matches)) {
                        $target = $matches [1];
                        $href = str_replace('&' . $matches [0], '', $href);
                    }

                    // 替换系统特殊字符串
                    $href = str_replace(array(
                        '[DELETE]',
                        '[EDIT]',
                        '[MODEL]'
                    ), array(
                        'modelDelete?id=[id]&model=[MODEL]',
                        'modelEdit?id=[id]&model=[MODEL]',
                        $model ['id']
                    ), $href);

                    // 替换数据变量
                    $href = preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use ($data) {
                        return $data [$match [1]];
                    }, $href);

                    // 兼容多种写法
                    if (strpos($href, '?') === false && strpos($href, '&') !== false) {
                        $href = preg_replace("/&/i", "?", $href, 1);
                    }
                    if ($show == '删除') {
                        $val [] = '<a class="confirm"   href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '">' . $show . '</a>';
                    } else if (strpos($href, "#") === 0) {
                        $val [] = '<a  data-target="' . $href . '" data-toggle="modal">' . $show . '</a>';
                    } else if (strpos($href, "javascript_") === 0) {
                        $val [] = '<a  target="' . $target . '" href="' . str_replace("javascript_", "javascript:", $href) . '">' . $show . '</a>';
                    } else {
                        $val [] = '<a  target="' . $target . '" href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '">' . $show . '</a>';
                    }
                }
            }
            $value = implode(' ', $val);
//            $value = <<<srt
//<span class="input-group-btn btn-left">
//											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
//											Action <i class="fa fa-angle-down"></i>
//											</button>
//											<ul class="dropdown-menu pull-right" role="menu">$value</ul>
//</span>
//srt;
        }
        return $value;
    }

    /**
     * check if the weixin is bind  student or teacher
     */
    protected function  checkWeixinBind()
    {
        $studentInfo = M("student")->where("openid= \"" . get_openid() . "\" and token=\"" . get_token() . "\"")->find();
        $teacherInfo = M("teacher")->where("openid= \"" . get_openid() . "\" and token=\"" . get_token() . "\"")->find();
        if (empty($studentInfo) && empty($teacherInfo)) {
            $this->display(T(MOBILE_PATH . 'schoolCenterBind'));
        }
    }

    /**
     * get the school info
     * @return [type] [description]
     */
    protected function  getSchoolInfo()
    {
        $Model = M("school");
        $map = array('token' => get_token());
        $info = $Model->where($map)->find();
        return $info;
    }


    /**
     * get the model value
     * @param $modelName
     * @param $map
     * @return string id:value
     */
    private function getFieldData($modelName, $map)
    {
        $extra = ':' . "\r\n";
        $list = M($modelName)->where($map)->select();

        foreach ($list as $v) {
            $extra .= $v ['id'] . ':' . $v ['name'] . "\r\n";
        }

        // return
        return $extra;
    }

    /**
     * return the page attention
     */
    public function wxSignPackage()
    {
        $appinfo = get_token_appinfo("gh_94ecad95d624");
        $jssdk = new \JsSdk($appinfo["appid"], $appinfo["secret"]);
        $signPackage = $jssdk->GetSignPackage($_SERVER['HTTP_REFERER']);
        $this->ajaxReturn($signPackage);
    }

    /**
     * 取得当前查询的表名
     * @return string
     */
    protected function getTableName()
    {
        if (!empty($this->listsTable)) {
            $name = $this->listsTable;
        } else {
            $name = get_table_name($this->model['id']);
        }

        // 返回
        return $name;
    }

}