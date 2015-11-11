<?php

namespace Addons\School\Controller;

class PayItemController extends SchoolBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_payitem' );
        parent::_initialize ();
    }
}
