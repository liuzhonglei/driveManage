<?php

namespace Addons\School\Controller;

class PayitemController extends SchoolBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_payitem' );
        parent::_initialize ();
    }
}
