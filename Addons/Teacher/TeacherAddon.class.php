<?php

namespace Addons\Teacher;
use Common\Controller\Addon;

/**
 * 教练管理插件
 * @author 丑陋虚
 */

    class TeacherAddon extends Addon{

        public $info = array(
            'name'=>'Teacher',
            'title'=>'教练管理',
            'description'=>'管理驾校教练',
            'status'=>1,
            'author'=>'丑陋虚',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Teacher/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Teacher/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }