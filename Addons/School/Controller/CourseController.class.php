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
}
