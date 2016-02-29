<?php

namespace Addons\Car;

use Common\Controller\Addon;

/**
 * 车辆管理插件
 * @author 丑陋虚
 */
class CarAddon extends Addon
{

    public $info = array(
        'name' => 'Car',
        'title' => '车辆管理',
        'description' => '管理驾校的车辆相关信息',
        'status' => 1,
        'author' => '丑陋虚',
        'version' => '0.1',
        'has_adminlist'=>1,
        'type'=>1
    );

    public function install()
    {

        return true;
    }

    public function uninstall()
    {

        return true;
    }

    //实现的weixin钩子方法
    public function weixin($param)
    {

    }

}