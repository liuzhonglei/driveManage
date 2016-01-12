<?php

namespace Addons\Student\Controller;

class FieldDisplayConf extends StudentBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'field_display_conf' );
    }

}
