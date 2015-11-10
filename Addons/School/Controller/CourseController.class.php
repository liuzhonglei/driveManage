<?php

namespace Addons\School\Controller;

class CourseController extends SchoolBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_course' );
        parent::_initialize ();
    }
}
