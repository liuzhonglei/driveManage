<?php

namespace Addons\QingQing;

use Common\Controller\Addon;

/**
 * 驾校管理插件
 * @author 丑陋虚
 */
class QingQingAddon extends Addon
{
    public $info = array(
        'name' => 'QingQing',
        'title' => '轻轻平台',
        'description' => '轻轻平台管理相关信息',
        'status' => 1,
        'author' => '丑陋虚',
        'version' => '0.1',
        'has_adminlist' => 1,
        'type' => 1
    );

    //必须实现安装
    public function install()
    {
        return true;
    }

    //必须卸载插件方法
    public function uninstall()
    {
        return true;
    }

    //实现的weixin钩子方法
    public function weixin($param)
    {

    }
}