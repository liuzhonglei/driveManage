<?php

/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 16/2/5
 * Time: 11:02
 */

namespace Addons\EO2OPayment\Controller;

/**
 * 划款清单
 * Class EO2OPayListController
 * @package Addons\EO2OPayment\Controller
 */
class EO2OPayListController extends EO2OBaseController
{
    /**
     * init
     */
    function _initialize()
    {
        parent::_initialize();
        $this->model = $this->getModel('eo2o_pay_list');
    }

    /**
     * 扩展列表
     * @param null $model
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = 'id desc', $showPrivilege = true)
    {

        // 查询
        $list_data = parent::_get_model_list($model, $page, $order);

        // 转换
        $list_data ['list_data'] = $this->convertListField($list_data ['list_data'], 'payitem_id', 'payitem_id', 'school_payitem', 'id', "name");

        // 修正其他查询
        return $list_data;
    }


    /**
     *  条件语句
     * @param $model
     * @param $fields
     * @return array|string
     */
    public function _search_map($model, $fields)
    {
        $param = parent::_search_map($model, $fields);
        $param["result_code"] = array("IN", array('SUCCESS', 'WAIT'));
        if (!empty($_GET["student_id"])) {
            $param["student_id"] = $_GET["student_id"];
        }
        return $param;
    }


    /**
     * 扩展信息
     * @param $fields
     * @return \Addons\School\Controller\Common\Controller\当前的字段
     */
    public function getFieldList($fields)
    {
        $fields = parent::getFieldList($fields);
        $fields = $this->setFiledExtra($fields, "payitem_id", 'school_payitem', 'name', array("token" => get_token()));
        return $fields;
    }


    /**
     * 取得数据
     * @param bool|true $ajaxReturn
     * @return mixed
     */
    public function getModelDataById($ajaxReturn = true)
    {
        $data = parent::getModelDataById(false);
        $data["total_fee"] = $data["total_fee"] / 100;
        $this->ajaxReturn($data);
    }

    /**
     * 保存
     */
    public function  saveAdmin()
    {
        $this->model = $this->getModel('eo2o_payment');
        $_POST["total_fee"] = $_POST["total_fee"] * 100;
        $_POST["result_code"] || $_POST["result_code"] = "WAIT";
        parent::saveAdmin();
    }
}
