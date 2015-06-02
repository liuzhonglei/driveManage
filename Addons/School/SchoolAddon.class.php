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
        'name' => 'School',
        'title' => '驾校管理',
        'description' => '管理驾校的相关信息',
        'status' => 1,
        'author' => '丑陋虚',
        'version' => '0.1',
        'has_adminlist'=>1,
        'type'=>1
    );

    public function install()
    {
        $install_file = array('./Addons/School/Sql/install.sql','./Addons/School/Sql/install_view.sql');

       foreach($install_file as $file){
           if (file_exists($file)) {
               execute_sql_file($file);
           }
       }

        return true;
    }

    public function uninstall()
    {
        $uninstall_file = array('./Addons/School/Sql/uninstall.sql','./Addons/School/Sql/uninstall_view.sql');

        foreach($uninstall_file as $file){
            if (file_exists($file)) {
                execute_sql_file($file);
            }
        }

        return true;
    }

    //实现的weixin钩子方法
    public function weixin($param)
    {

    }

}