<?php

namespace Addons\School\Controller;
/**
 * info controller
 *
 */
class InfoController extends SchoolBaseController{
	/*
	 * init
	 */
    function _initialize() {
        parent::_initialize ();
        $this->model = $this->getModel ( 'school_info' );
        $this->dataMultiEdit = true;
    }

     /**
     * show the schoolInfo page
     * @return the page
     */
    function schoolInfo()
    {
        $this->assign($this->getSchoolInfo());
        $data = M('school_info')->where('token = "' . get_token() . '" and type = 2')->find();
        $this->assign($data);
        $this->display(T(MOBILE_PATH.'schoolInfo'));
    }
}
