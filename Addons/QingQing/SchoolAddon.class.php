<?php

namespace Addons\school;

use Common\Controller\Addon;

/**
 * 驾校管理插件
 * @author 丑陋虚
 */
class schoolAddon extends Addon
{
    public $info = array(
        'name' => 'QingQing',
        'title' => '轻轻平台管理',
        'description' => '轻轻平台管理相关信息',
        'status' => 1,
        'author' => '丑陋虚',
        'version' => '0.1',
        'has_adminlist'=>1,
        'type'=>1
    );
    //必须实现安装
    public function install(){

    }

    //必须卸载插件方法
    public function uninstall(){

    }
}