<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

use Home\Controller\AddonsController;
use Think\Model;

/**
 * Class SchoolBaseController
 * @package Addons\School\Controller
 */
class SchoolBaseController extends AddonsController
{
    /**
     * inint
     */
    function _initialize()
    {
        parent::_initialize();

        $controller = strtolower(_CONTROLLER);

        $res ['title'] = '驾校信息';
        $res ['url'] = addons_url('School://School/lists');
        $res ['class'] = ($controller == 'school' || $controller == 'photo') ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '相关信息';
        $res ['url'] = addons_url('School://Info/lists');
        $res ['class'] = $controller == 'info' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '课程信息';
        $res ['url'] = addons_url('School://Course/lists');
        $res ['class'] = $controller == 'course' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '费用信息';
        $res ['url'] = addons_url('School://Payitem/lists');
        $res ['class'] = $controller == 'payitem' ? 'current' : '';
        $nav [] = $res;


        $res ['title'] = '驾校场地';
        $res ['url'] = addons_url('School://Place/lists');
        $res ['class'] = $controller == 'place' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '优惠资讯';
        $res ['url'] = addons_url('School://Privilege/lists');
        $res ['class'] = $controller == 'privilege' ? 'current' : '';
        $nav [] = $res;

        $this->assign('nav', $nav);

        // set if
        if (i('model') == 'teacher') {
            $this->assign('nav', null);
        }
    }
}