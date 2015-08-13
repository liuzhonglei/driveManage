<?php

namespace Addons\School\Controller;
use Home\Controller\AddonsController;

class CourseController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_course' );
        parent::_initialize ();
    }
}
