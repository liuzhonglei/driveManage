<?php

namespace Addons\QingQing\Model;

use Home\Model\WeixinModel;

/**
 * school的微信模型
 */
class WeixinAddonModel extends WeixinModel
{
    // 关注公众号事件
    public function subscribe()
    {
        return true;
    }

    // 取消关注公众号事件
    public function unsubscribe()
    {
        return true;
    }

    // 扫描带参数二维码事件
    public function scan()
    {
        return true;
    }

    // 上报地理位置事件
    public function location()
    {
        return true;
    }

    // 自定义菜单事件
    public function click()
    {
        return true;
    }
}
        	