<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/9/12
 * Time: 15:36
 */

namespace Addons\School\Controller;

/**
 * Class AdminController
 * @package Addons\School\Controller
 */
class AdminController extends BaseController
{

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
                }else if (in_array($fields[$i][$j]['name'], array('course_id'))) {
                    if (empty($teacherData)) {
                        $courseData = $this->getFieldData('school_course', array('token' => get_token()));
                    }
                    $fields[$i][$j] ['extra'] .= "\r\n" . $courseData;
                }else if(in_array($fields[$i][$j]['name'], array('school_place_id'))){
                    if (empty($teacherData)) {
                        $courseData = $this->getFieldData('school_course', array('token' => get_token()));
                    }
                    $fields[$i][$j] ['extra'] .= "\r\n" . $courseData;
                }
            }
        }

        // return
        $this->ajaxReturn($fields);
    }

    /**
     * get the model value
     * @param $modelName
     * @param $map
     * @return string id:value
     */
    private  function getFieldData($modelName, $map)
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
     * get the model fields
     * @param $model
     * @return the model fields
     */
    public function getModelFields($model)
    {
        $model || $model = $this->getModel(I('model')); // 默认显示第一页数据
        $list_data = $this->_list_grid($model);
        $this->ajaxReturn($list_data);
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
        $id || $id = i("id");

        // search
        $info = M($model)->where('id="'.$id.'" and token= "'.$token.'" ')->find();
        if (!empty($info["photo"])) {
            $info["photoUrl"] = get_cover($info["photo"])['path'];
        }else{
            $info["photoUrl"] = "";
        }

        return $this->ajaxReturn($info);
    }

    /**
     * get the model data
     * @param null $model
     * @param int $page
     * @param string $order
     */
    public function getDataList($model = null, $page = 0, $order = 'id desc')
    {
        // page and length
        $custromActionType = i("custromActionType");
        $customActionName = i("customActionName");
        $start = I('start', 0, 'intval');
        $row = I('length', 1, 'intval');
        $model || $model = $this->getModel($_GET['model']);

        // if remove some data
        if ($customActionName == "delete") {
            $_POST['ids'] = $_POST['id'];
            unset($_REQUEST['id']);
            unset($_POST['id']);
            if (!$this->commonDel($model)) {
                $this->error("数据删除失败");
            }
        }

        // page and row
        $page = $start / $row + 1;
        $row || $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];

        // model and  fields

        $list_data = $this->_list_grid($model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // search
        $map = $this->_search_map($model, $fields);

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


        // 读取模型数据列表
        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');
        $name = get_table_name($model ['id']);
        foreach (self::$tableNames as $handleName) {
            if (!strcmp($handleName, $name)) {
                $name .= '_all';
                break;
            }
        }
        $data = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order($order)->page($page, $row)->select();

        // count
        $count = M($name)->where($map)->count();

        // convert
        $list_data ['data'] = array();
        foreach ($data as $record) {
            $convertRecord = array_values($record);
            for ($i = 0; $i < count($record); $i++) {
                $convertRecord[$i] = $this->get_list_field($record, $grids[$i], $model);
            }
            array_unshift($convertRecord, '<input type="checkbox" name="id[]" value="' . $record['id'] . '">');
            $list_data ['data'][] = $convertRecord;
        }

        $list_data ['recordsTotal'] = $count;
        $list_data["recordsFiltered"] = $count;
        $list_data["draw"] = i("draw");


        // return
        $this->ajaxReturn($list_data);
    }

    /**
     * save the post data
     * @return bool
     */
    public function saveModel($model)
    {
        //param
        $model || $model = $_GET["model"];
        $model || $model = $this->model['id'];
        $id =  i('id');

        // process
        $Model = M($model);
        $Model = $this->checkAttr($Model,$model);
        if (empty(i('id'))) {
            $result = $Model->create() && $id = $Model->add();
        } else {
            $result = $Model->create() && $id = $Model->save();
        }
        if ($result) {
            $this->_saveKeyword($this->model, $id);
            $result = array("status" => "1", "msg" => '保存成功');
        }else{
            $result = array("status" => "0", "msg" => "保存失败！");
        }

        // return
        $this->ajaxReturn($result);
    }

    /**
     *
     * @param null $model
     * @param string $templateFile
     */
    public function modelAdd($model = null, $templateFile = '')
    {
        is_array($model) || $model = $this->getModel($model);
        if (IS_POST) {
            $Model = D(parse_name(get_table_name($model ['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $model ['id']);
            if ($Model->create() && $id = $Model->add()) {
                $this->_saveKeyword($model, $id);

                $this->success('添加' . $model ['title'] . '成功！', U('lists?model=' . $model ['name'], $this->get_param));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model ['id']);
            $this->assign('fields', $fields);
            $this->meta_title = '新增' . $model ['title'];

            $templateFile || $templateFile = $model ['template_add'] ? $model ['template_add'] : '';
            $this->display($templateFile);
        }
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
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }

    }
//
//    /**
//     * admin edit
//     */
//    public function modelEdit($model = null, $id = 0)
//    {
//        $modelData = json_decode(file_get_contents("php://input"));
//        if (!empty($modelData)) {
//            foreach ($modelData as $key => $value) {
//                $_REQUEST[$key] = $value;
//            }
//        }
//
//        is_array($model) || $model = $this->getModel($model);
//        $id || $id = I('id');
//
//        // 获取数据
//        $data = M(get_table_name($model ['id']))->find($id);
//        $data || $this->error('数据不存在！');
//
////        $token = get_token ();
////        if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
////            $this->error ( '非法访问！' );
////        }
//
//        if (IS_POST) {
//            $Model = D(parse_name(get_table_name($model ['id']), 1));
//            // 获取模型的字段信息
//            $Model = $this->checkAttr($Model, $model ['id']);
//            if ($Model->create() && $Model->save()) {
//                $this->_saveKeyword($model, $id);
//                $result = array("code" => "0", "msg" => '保存' . $model ['title'] . '成功！', U('lists?model=' . $model ['name'], $this->get_param));
//                $this->ajaxReturn($result);
//            } else {
//                $msg = $Model->getError() | "操作失败";
//                $result = array("code" => "-1", "msg" => $msg);
//            }
//            $this->ajaxReturn($result);
//        } else {
////            $fields = get_model_attribute ( $model ['id'] );
////
////            $this->assign ( 'fields', $fields );
////            $this->assign ( 'data', $data );
////            $this->meta_title = '编辑' . $model ['title'];
////
////            $templateFile || $templateFile = $model ['template_edit'] ? $model ['template_edit'] : '';
////            $this->display ( $templateFile );
//
//            $this->ajaxReturn($data);
//        }
//    }


    /* 解析列表定义规则 */
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
                        $val [] = '<li><a class="confirm"   href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '">' . $show . '</a></li>';
                    } else if (strpos($href, "#") === 0) {
                        $val [] = '<li><a  data-target="' . $href . '" data-toggle="modal">' . $show . '</a></li>';
                    } else if (strpos($href, "javascript_") === 0) {
                        $val [] = '<li><a  target="' . $target . '" href="' . str_replace("javascript_", "javascript:", $href) . '">' . $show . '</a></li>';
                    } else {
                        $val [] = '<li><a  target="' . $target . '" href="' . urldecode(U($href, $GLOBALS ['get_param'])) . '">' . $show . '</a></li>';
                    }
                }
            }
            $value = implode(' ', $val);
            $value = <<<srt
<span class="input-group-btn btn-left">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
											Action <i class="fa fa-angle-down"></i>
											</button>
											<ul class="dropdown-menu pull-right" role="menu">$value</ul>
</span>
srt;
        }
        return $value;
    }

}