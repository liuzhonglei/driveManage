<?php

namespace Addons\School\Controller;

class PayitemController extends SchoolBaseController{
    function _initialize() {
        parent::_initialize ();
        $this->model = $this->getModel ( 'school_payitem' );
        $this->dataMultiEdit = true;
    }
}
