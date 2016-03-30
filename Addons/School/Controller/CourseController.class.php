<?php

namespace Addons\School\Controller;

class CourseController extends SchoolBaseController{
    function _initialize() {
        parent::_initialize ();
        $this->model = $this->getModel ( 'school_course' );
        $this->dataMultiEdit = true;
    }

    /**
     * get the teacher course
     * @return mixed
     */
    function getCourseList()
    {
        $params = array("token" => get_token());
        $info = M('school_course')->where($params)->select();

        foreach ($info as &$vo) {
            $vo ['sign_charge'] = $this->getActivityPrice( $vo ['name'], $vo ['sign_charge']);
        }

        return $info;
    }

    /**
     * 修改可选择下拉框
     * @param $fields
     * @return Common\Controller\当前的字段
     */
    public function getFieldList($fields)
    {
        $fields =  parent::getFieldList($fields);
        $fields = $this->setFiledExtra($fields, "learn_pay_item_id", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"sign"));
        $fields = $this->setFiledExtra($fields, "learn_pay_item_id_2", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"sign"));
        $fields = $this->setFiledExtra($fields, "link_charge_item_id", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"sign"));


        $fields = $this->setFiledExtra($fields, "learn_stage_pay_item_id", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"sign"));
        $fields = $this->setFiledExtra($fields, "km1_make_up_pay_item_id", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"supplementary"));
        $fields = $this->setFiledExtra($fields, "km1_twice_make_up_pay_item_id", 'school_payitem', 'name',array("token"=>get_token(),"type"=>"supplementary"));


        // 返回
        return $fields;
    }
}
