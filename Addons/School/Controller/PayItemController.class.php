<?php

namespace Addons\School\Controller;
use Home\Controller\AddonsController;

class PayitemController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_payitem' );
        parent::_initialize ();
    }
}
