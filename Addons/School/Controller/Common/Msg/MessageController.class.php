<?php
namespace Addons\School\Controller\Common\Msg;

use Addons\Abcinfosend\Api\Wechat;
use Addons\School\Controller\BaseController;

/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 16/2/14
 * Time: 14:08
 */
class MessageController extends BaseController
{
    /**
     * 初始化
     */
    function _initialize()
    {
        $this->model = $this->getModel('weixin_message');
        parent::_initialize();
    }

    /**
     * 发送微信模板信息
     */
    function sendWeixinMessage($token,$openid,$tempId,$tempData)
    {
        // 创建对象
        $token || $token = get_token();
        $info = M('member_public')->where(array(token=>$token))->find();
        $this->assign('info', $info);
        $options = array(
            'token' => 'weiphp', //填写你设定的key,本系统中固定weiphp
            'appid' => $info['appid'],
            'appsecret' => $info['secret'],
        );
        $weObj = new Wechat($options);

        // 发送
        return  $weObj->sendTempMes($openid, $tempId, $tempData);
    }
}