<?php

namespace Addons\GroupBuy;

use Common\Controller\Addon;

/**
 * 砍价活动插件
 * @author 丑陋虚
 */
class GroupBuyAddon extends Addon
{

    public $info = array(
        'name' => 'GroupBuy',
        'title' => '团购活动插件',
        'description' => '驾校的团购活动插件',
        'status' => 1,
        'author' => '丑陋虚',
        'version' => '0.1',
        'has_adminlist'=>1,
        'type'=>1
    );

    public function install() {
        $install_sql = './Addons/GroupBuy/install.sql';
        if (file_exists ( $install_sql )) {
            execute_sql_file ( $install_sql );
        }
        return true;
    }
    public function uninstall() {
        $uninstall_sql = './Addons/GroupBuy/uninstall.sql';
        if (file_exists ( $uninstall_sql )) {
            execute_sql_file ( $uninstall_sql );
        }
        return true;
    }

    //实现的weixin钩子方法
    public function weixin($param){

    }
}