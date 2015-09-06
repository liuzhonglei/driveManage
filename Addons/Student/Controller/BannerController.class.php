<?php

namespace Addons\Student\Controller;

/**
 * Class QuestionController
 * @package Addons\Student\Controller
 */
class BannerController extends StudentBaseController{
    /**
     * init
     */
    function _initialize() {
        $this->model = $this->getModel ( 'student_banner' );
        parent::_initialize ();
    }

    /**
     * list
     */
    function lists(){
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        parent::common_lists($this->model);
    }
}
