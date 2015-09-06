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
        $_POST['time'] = date("Y-m-d");
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
        $name = i("name");
        if(empty($name)){
            $token = "gh_94ecad95d624";
//            if(empty($token) || $token == -1){
//                $token = gh_94ecad95d624;
//            }

//            http://www.iqqxc.com/index.php?s=/home/Index/leaflets/token/gh_94ecad95d624.html
           // redirect("http://mp.weixin.qq.com/s?__biz=MzIyNzA0NjczMw==&mid=218779399&idx=1&sn=304e5822cebbc5c928a86ab7a22cd9a7&scene=0#rd");
            $this->display( T ("Addons://QingQing@Mobile/"."color"));
        }else{
            $this->display( T ("Addons://QingQing@Mobile/".$name));
        }
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
        redirect("http://a.app.qq.com/o/simple.jsp?pkgname=com.sihan.foxcard.android");
    }
}
