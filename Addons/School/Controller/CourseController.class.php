<?php

namespace Addons\School\Controller;

class CourseController extends SchoolBaseController{
    function _initialize() {
        parent::_initialize ();
        $this->model = $this->getModel ( 'school_course' );
        $this->dataMultiEdit = true;
    }
}
