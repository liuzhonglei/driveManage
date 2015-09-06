<?php

namespace Addons\EO2OPayment;
use Common\Controller\Addon;

/**
 * 鑫无线支付插件插件
 * @author 鑫无线
 */

    class EO2OPaymentAddon extends Addon{

        public $info = array(
            'name'=>'EO2OPayment',
            'title'=>'支付信息',
            'description'=>'支持微信支付，参考Controller/EO2OPaymentController 里面的PayTo 使用',
            'status'=>1,
            'author'=>'鑫无线',
            'version'=>'0.1',
            'has_adminlist'=>0,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/EO2OPayment/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/EO2OPayment/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }