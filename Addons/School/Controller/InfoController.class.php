<?php

namespace Addons\School\Controller;
use Home\Controller\AddonsController;

class InfoController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_info' );
        parent::_initialize ();
    }
}
