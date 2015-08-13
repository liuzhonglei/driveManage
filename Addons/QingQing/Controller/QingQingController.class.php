<?php

namespace Addons\QingQing\Controller;
require_once (dirname(__FILE__).'/../Public/Weixin/JSSDK.php');
define ( 'QINGQING_MOBILE_PUBLIC_PATH', __ROOT__."/Addons/QingQing/View/default/Mobile");
use Addons\School\Controller\BaseController;

class QingQingController extends BaseController
{
    function _initialize()
    {
        parent::_initialize();

        // the navigator`
        $controller = strtolower(_CONTROLLER);
        $res ['title'] = '红包信息';
        $res ['url'] = addons_url('QingQing://QingQing/lists');
        $res ['class'] = ($controller == 'Qing') ? 'current' : '';
        $nav [] = $res;
        $this->assign('nav', $nav);

        // set the model
        $this->model = $this->getModel('qingqing_coupon');
    }

    /**
     * add goupon
     */
    function addGoupon()
    {
        $info = M($this->model['name'])->where('name = "'.i('name').'" and phone = "'.i('phone').'" ')->find();
        if(!empty($info)){
            $this->error("您已报名，请勿重复提交！");
        }

        // add the data
        $_POST['openid'] = get_openid();
        $_POST['token'] = get_token();
        $_POST['time·'] = date("Y-m-d");
        parent::common_add($this->model);
    }

    /**
     * return the page attention
     */
    function pageAttention(){
        $appinfo =  get_token_appinfo("gh_94ecad95d624");
        $jssdk = new \JsSdk($appinfo["appid"], $appinfo["secret"]);
        $signPackage = $jssdk->GetSignPackage($_SERVER['HTTP_REFERER']);
        $this->ajaxReturn($signPackage);
    }

    /**
     * return the page attention
     */
    function game(){
        $this->display( T ("Addons://QingQing@Mobile/color"));
    }

    /**
     * return the page attention
     */
    function ballGame(){
        $this->display( T ("Addons://QingQing@Mobile/ball"));
    }

    /**
     * return the page attention
     */
    function download(){
        dispay("http://down2.uc.cn/pp/down.php?pub=PM_2056");
    }
}
