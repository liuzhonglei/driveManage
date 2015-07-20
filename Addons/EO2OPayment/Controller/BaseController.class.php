<?php

namespace Addons\EO2OPayment\Controller;
use Home\Controller\AddonsController;


class BaseController extends AddonsController {
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );
	
		$res ['title'] = '客户';
		$res ['url'] = addons_url ( 'EO2OPayment://Eo2oPaymentList/lists' );
		$res ['class'] = $controller == 'eo2opaymentlist' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '系统设置';
		$res ['url'] = addons_url ( 'EO2OPayment://EO2OPayment/config' );
		$res ['class'] = $controller == 'eo2opayment' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
		$config = getAddonConfig ( 'EO2O' );
		$config ['background_url'] = $config ['background'] == 11 ? $config ['background_custom'] : ADDON_PUBLIC_PATH . '/card_bg_' . $config ['background'] . '.png';
		$this->assign ( 'config', $config );
		//dump ( $config );
		//dump(get_token());
	}
	
	protected function log($logtxt)
	{
		$time = date('Y-m-d H:i:s',time());
		
		file_put_contents('log.txt', $time.':'.$logtxt.PHP_EOL,FILE_APPEND);
	}
}