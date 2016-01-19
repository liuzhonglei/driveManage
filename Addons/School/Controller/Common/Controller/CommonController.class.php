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


class CommonController extends ExtendAddonsController
{

    // 当前model对象
    var $model;

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
            $result = array("status" => "1", "info" => '保存成功', 'id' => $id);
        } else {
            $result = array("status" => "0", "info" => "保存失败！" . $Model->getError());
        }

        // return
        return $result;
    }


    /**
     * 取得字段列表（信息)）
     * @param $model the model name
     */
    public function getFieldList($fields)
    {
        // set the extra data
        for ($i = 1; $i <= count($fields); $i++) {
            for ($j = 0; $j < count($fields[$i]); $j++) {
                if (in_array($fields[$i][$j]['name'], array('id_teacher', 'id_teacher_k1', 'id_teacher_k2', 'id_teacher_k3', 'id_in_teacher'))) {
                    if (empty($teacherData)) {
                        $teacherData = $this->getFieldData('teacher', array('status' => 1, 'token' => get_token()));
                    }
                    $fields[$i][$j] ['extra'] .= "\r\n" . $teacherData;
                } else if (in_array($fields[$i][$j]['name'], array('course_id'))) {
                    $courseData = $this->getFieldData('school_course', array('token' => get_token()));
                    $fields[$i][$j] ['extra'] .= "\r\n" . $courseData;
                } else if (in_array($fields[$i][$j]['name'], array('school_place_id'))) {
                    $schoolPlaceData = $this->getFieldData('school_place', array('token' => get_token()));
                    $fields[$i][$j] ['extra'] .= "\r\n" . $schoolPlaceData;
                }
            }
        }

        // return
        return $fields;
    }


    /**
     * 当下拉框数据位其他表的数据室，调用进行设置附加值
     * @param $fields 当前的字段
     * @param $fieldName 要修改的字段名
     * @param $model 要查询的模型名称
     * @param $showFiled 要显示的模型字段
     * @param $selectd 是否默认选中第一个数据
     * @param @map 查询条件
     */
    protected function setFiledExtra($fields, $fieldName, $model, $showFiled, $map = null,$selected = false)
    {
        if (empty($map)) {
            array('token' => get_token());
        }

        for ($i = 1; $i <= count($fields); $i++) {
            for ($j = 0; $j < count($fields[$i]); $j++) {
               $fieldInfo =   $fields[$i][$j];
                if ($fieldInfo['name'] == $fieldName) {
                    $extraData = $this->getFieldData($model, $map, $showFiled,$fieldInfo['is_must']=="1");
                    $fieldInfo ['extra'] = $extraData;
                    if($selected && empty($fieldInfo["value"])){
                        $fieldInfo["value"]  = explode(":",explode(",",$extraData)[0])[0];
                    }
                    $fields[$i][$j] = $fieldInfo;
                    return $fields;
                }
            }
        }

        return $fields;
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


    /**
     * 取得可选信息（查看信息)）
     * @param $modelName
     * @param $map
     * @param $showField 显示字段
     * @return string id:value
     */
    private function getFieldData($modelName, $map, $showField = 'name', $allowNull = true)
    {
        $extra = "";
        if ($allowNull) {
            $extra += "'':空\r\n";
        }

        $list = M($modelName)->where($map)->select();

        foreach ($list as $v) {
            $extra .= $v ['id'] . ':' . $v [$showField] . "\r\n";
        }

        // return
        return $extra;
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
     * get the data by id
     * @param null $model
     * @param int $id
     */
    public function getDataById($model = null, $id = 0)
    {
        // param
        $token = get_token();
        $model || $model = i("model");
        $model || $model = $this->getModel()['name'];
        $id || $id = i("id");

        // search
        $info = M($model)->where('id="' . $id . '" and token= "' . $token . '" ')->find();
        if (!empty($info["photo"])) {
            $info["photoUrl"] = get_cover($info["photo"])['path'];
        } else {
            $info["photoUrl"] = "http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image";
        }

        return $info;
    }


    /**
     * get the model info
     */
    public function getModelInfo($ajaxReturn = true)
    {
        //设置扩展
        $this->setAdminModel();

        // 得到model
        $model = $this->model;
        // get the  fields
        $model || $model = $this->getModel($_GET['model']); // 默认显示第一页数据

        // list_data
        $list_data = $this->_list_grid($model);
        if ($this->dataMultiEdit) {
            array_unshift($list_data['fields'], 'select');
            array_unshift($list_data['list_grids'], array("title" => '<input type="checkbox" class="group-checkable">'));
        }

        // fieldList
        $fields = get_model_attribute($model['id']);
        $fieldList = $this->getFieldList($fields);

        // result
        $result = array();
        $result['model'] = $model;
        $result['fieldList'] = $fieldList;
        $result['list_data'] = $list_data;

        // return
        if ($ajaxReturn) {
            $this->ajaxReturn($result);
        } else {
            return $result;
        }
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
     * 使用模糊查询查询数据集合
     * @param $model
     * @param $getFields
     * @param $searchFieldList
     * @param $text
     * @return mixed
     */
    public function getModelDataList($model, $getFields, $searchFieldList, $text)
    {
        $sql = "select " . $getFields . " from wp_" . $model . " t where t.token = '" . get_token() . "' and (";
        $fieldMap = "";
        foreach ($searchFieldList as $field) {
            if (empty(!$fieldMap)) {
                $fieldMap .= "or ";
            }
            $fieldMap .= $field . " like '%" . $text . "%' ";
        };
        $sql .= $fieldMap . " )";

        // 反悔
        return M($model)->query($sql);
    }

    /**
     * 转换外键信息为对应的栏位信息
     * 在列表显示中使用
     */
    protected function  convertListField($list, $idField, $showField, $model, $modelId, $fieldName = null)
    {
        $result = array();
        $fieldName || $fieldName = $showField;
        foreach ($list as $item) {
            $info = M($model)->where(array("token" => get_token(), $modelId => $item[$idField]))->find();
            $item[$showField] = $info[$fieldName];
            array_push($result, $item);
        }

        // 反悔
        return $result;
    }
}