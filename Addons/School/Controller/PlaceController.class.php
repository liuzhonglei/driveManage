<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 17:17
 */

namespace Addons\School\Controller;


class PlaceController extends SchoolBaseController{

        function _initialize() {
            parent::_initialize ();
            $this->model = $this->getModel ( 'school_place' );
            $this->dataMultiEdit = true;

        }
}