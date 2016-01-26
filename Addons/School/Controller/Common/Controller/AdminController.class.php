<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/9/12
 * Time: 15:36
 */

namespace Addons\School\Controller\Common\Controller;

/**
 * Class AdminController
 * @package Addons\School\Controller
 */
class AdminController extends CommonController
{
    function _initialize()
    {
        parent::_initialize();
        if ($this->isAdmin() && !is_login()) {
            $this->adminReturn(-1, "未登录");
        }
        if ($this->isAdmin()) {
            // 设置操作栏
            $this->setAdminModel();
        }
    }

    /**
     * 是否登录
     */
    function isLogin(){
        if ($this->isAdmin() && !is_login()) {
            $this->success("已登录");
        }else{
            $this->error("未登录");
        }
    }


    // 列表多选标志
    var $dataMultiEdit = false;

    /**
     * 设置对应的操作字段
     */
    protected function setAdminModel()
    {
        if (!empty($this->model['list_grid_admin'])) {
            $this->model['list_grid'] = $this->model['list_grid_admin'];
        }
    }



    public function add()
    {
        if ($this->isAdmin()) {
            $this->saveAdmin();
        } else {
            parent::add($this->model);
        }
    }

    public function edit()
    {
        if ($this->isAdmin()) {
            $this->saveAdmin();
        } else {
            parent::common_edit($this->model);
        }
    }

    public function lists()
    {
        if ($this->isAdmin()) {
            $this->listsAdmin();
        } else {
            parent::lists();
        }
    }


    /**
     * get the model data
     * @param null $model
     * @param int $page
     * @param string $order
     */
    public function listsAdmin($ajaxReturn = true,$map = null)
    {
        // 设置操作栏
        $this->setAdminModel();

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

        // 设置操作列表

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
            // 字段名称为空
            if (empty($fields[$orderItem["column"]])) {
                continue;
            }

            // 配置对应的数据
            if (!empty($order)) {
                $order .= ", ";
            }
            $order .= $fields[$orderItem["column"]] . " " . $orderItem["dir"];
        }

        // list data
        $list_data = $this->_get_model_list($model, $page, $order,$map);

        // convert
        $list_data ['data'] = array();
        $length = count($list_data ['list_data']);
        for ($index = 0; $index < $length; $index++) {
            $record = $list_data ['list_data'][$index];
            $convertRecord = array_values($record);
            // 配置操作栏显示
            $show = true;
            if ($index < $length / 2) {
                $show = false;
            }
            for ($i = 0; $i < count($record); $i++) {
                $convertRecord[$i] = $this->get_list_field($record, $list_data ['list_grids'][$i], $model, $show);
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
        if ($ajaxReturn) {
            $this->ajaxReturn($list_data);
        } else {
            return $list_data;
        }
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
     * admin save
     */
    public function  saveAdmin()
    {
        $this->saveModel();
        $this->success();
    }

    /**
     * 判断是否是ADMIN界面
     */
    protected function isAdmin()
    {
        if (strpos($_SERVER['HTTP_REFERER'], "admin")) {
            return true;
        }
        return false;
    }

    /**
     * 返回报错
     * @param string $message
     * @param string $jumpUrl
     * @param bool $ajax
     */
    protected function error($message = '', $jumpUrl = '', $ajax = false)
    {
        if ($this->isAdmin() && empty($jumpUrl)) {
            $this->adminReturn(0, $message);
        } else {
            parent::error($message, $jumpUrl, $ajax); // TODO: Change the autogenerated stub

        }

    }

    /**
     * 返回成功
     * @param string $message
     * @param string $jumpUrl
     * @param bool $ajax
     */
    protected function success($message = '', $jumpUrl = '', $ajax = false, $data = null)
    {
        if ($this->isAdmin()) {
            $this->adminReturn(1, $message, $data);
        } else {
            parent::success($message, $jumpUrl, $ajax); // TODO: Change the autogenerated stub
        }
    }


    /**
     * admin 结果返回
     * @param $result
     * @param $message
     */
    private function adminReturn($result, $message = null, $data = null)
    {
        $map = array(result => $result, message => $message, data => $data);
        $this->ajaxReturn($map);
    }


    /**
     * admin delete
     */
    public function deleteAdmin($model)
    {
        $this->ajaxReturn($this->common_del($model));
    }


    /**
     * 解析列表定义规则(数据格式转换)
     * @param $data
     * @param $grid
     * @param $model
     * @return mixed|string
     */
    function get_list_field($data, $grid, $model, $dropup = false)
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
                        'deleteAdmin?id=[id]&model=[MODEL]',
                        'editAdmin?id=[id]&model=[MODEL]',
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
                    if (strpos($href, "link_") === 0) {
                        $val [] = '<a  target="' . $target . '" href="' . str_replace("javascript_", "javascript:", str_replace("link_", "", $href)) . '"> ' . $value . ' </a>';
                    } else {
                        if (strpos($href, "javascript_") === 0) {
                            $colors = array("blue");
                            $color = $colors[array_rand($colors, 1)];
                            $val [] = '<button type="button" class="btn ' . $color . ' btn-xs" target="' . $target . '" onclick="' . str_replace("javascript_", "javascript:", $href) . '"> ' . $show . ' </button>';
                        } else if ($show == '删除') {
                            $val [] = '<a class="confirm"   href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '"> ' . $show . ' </a>';
                        } else if (strpos($href, "#") === 0) {
                            $val [] = '<a  data-target="' . $href . '" data-toggle="modal">' . $show . '</a>';
                        } else {
                            $val [] = '<a  target="' . $target . '" href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '"> ' . $show . ' </a>';
                        }
                        if (!$this->operationIsShow($data, $show)) {
                            array_pop($val);
                        }
                    }
                }
            }
            $value = implode('', $val);
            $class = "";

            // 根据数据判断操作是否实现

            // 超过组合成一个下拉框
            if (count($val) > 5) {
                if ($dropup) {
                    $class = "dropup";
                }
                $value = implode('</li><li>', $val);
                $value = "<div class=\"btn-group " . $class . "\"> <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">相关操作</button> <ul class=\"dropdown-menu\" role=\"menu\"><li>" . $value . "</li></ul></div>";
            }
        }
        return $value;
    }

    /**
     * 判断操作是否出现
     * @param data 数据
     * @param opertaon 操作名称
     */
    protected function operationIsShow($data, $operation)
    {
        return true;
    }

    /**
     * get the data by id
     * @param null $model
     * @param int $id
     */
    public function getModelDataById($ajaxReturn = true)
    {
        if($ajaxReturn){
            $this->ajaxReturn($this->getDataById($this->model["name"]));
        }else{
            return $this->getDataById($this->model["name"]);
        }
    }


}