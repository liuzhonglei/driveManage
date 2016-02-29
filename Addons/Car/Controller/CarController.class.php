<?php
namespace Addons\Car\Controller;
use Addons\School\Controller\BaseController;

/**
 * the main school controller
 */

class CarController extends BaseController
{
    /**
     * 初始化
     */
    function _initialize()
    {
        $this->model = $this->getModel('car');
        $this->dataMultiEdit = true;

        parent::_initialize();
    }

}
