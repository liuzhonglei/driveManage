<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\Message\Controller;

use Addons\School\Controller\BaseController;


class MessageBaseController  extends BaseController{
    function _initialize() {
        parent::_initialize();

        $controller = strtolower ( _CONTROLLER );
        $action = strtolower ( _ACTION );

        $res ['title'] = '学员提问';
        $res ['url'] = addons_url ( 'Message://Message/lists' );
        $res ['class'] = ($controller == 'message' && $action == "lists") ? 'current' : '';
        $nav [] = $res;
        $res ['title'] = '配置信息';
        $res ['url'] = addons_url ( 'Message://Message/config' );
        $res ['class'] = ($controller == 'message' && $action == "config") ? 'current' : '';
        $nav [] = $res;
        $this->assign ( 'nav', $nav );
    }
}