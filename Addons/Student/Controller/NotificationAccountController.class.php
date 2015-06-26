<?php

namespace Addons\Student\Controller;
use Addons\Abcinfosend\Api\Wechat;

require_once ONETHINK_ADDON_PATH.'/Third/Controller/simple_html_dom.php';

class NotificationAccountController extends StudentBaseController{

    function _initialize() {
        $this->model = $this->getModel ( 'school_noti_account' );
        parent::_initialize ();

        // 子导航
        $action = strtolower(_ACTION);
        $controller = strtolower ( _CONTROLLER );
        $res ['title'] = '通知';
        $res ['url'] = addons_url('Student://Notification/lists');
        if($controller=='notification' && ($action=='lists' || $action=='sync' || $action== 'notifcation' || $action=='notiConfirm')){
            $res ['class'] = 'cur';
        }else{
            $res ['class'] = '';
        }
        $nav [] = $res;


        $res ['title'] = '帐号配置';
        $res ['url'] = addons_url('Student://NotificationAccount/lists');
        $res ['class'] = ($action=='lists' || $action=='sync' || $action== 'notifcation')? 'cur':'';
        if($controller=='notificationaccount'){
            $res ['class'] = 'cur';
        }else{
            $res ['class'] = '';
        }
        $nav [] = $res;


        $res ['title'] = '配置';
        $res ['url'] = addons_url('Student://Notification/config');
        $res ['class'] = ($action=='config')? 'cur':'';
        $nav [] = $res;

        // assign
        $this->assign('sub_nav', $nav);
    }

}
