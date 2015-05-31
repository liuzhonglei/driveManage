<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 17:17
 */

namespace Addons\School\Controller;


class PlaceController extends BaseController{

        function _initialize() {
            $this->model = $this->getModel ( 'school_place' );
            parent::_initialize ();
        }


}