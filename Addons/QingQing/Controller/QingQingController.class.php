<?php

namespace Addons\QingQing\Controller;
require_once (dirname(__FILE__).'/../Public/Weixin/JSSDK.php');

use Addons\School\Controller\BaseController;

class QingQingController extends BaseController
{
    function _initialize()
    {
        parent::_initialize();

        // the navigator
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
        parent::common_add($this->model);
    }

    /**
     * return the page attention
     */
    function pageAttention(){

        $appinfo =  get_token_appinfo(get_token());
        $jssdk = new \JsSdk($appinfo["appid"], $appinfo["secret"]);
        $signPackage = $jssdk->GetSignPackage($_SERVER['HTTP_REFERER']);
        $this->ajaxReturn($signPackage);
    }
}
