<?php

namespace Addons\EO2OPayment\Controller;
use Addons\School\Controller\BaseController;


/**
 * Class EO2OBaseController
 * @package Addons\EO2OPayment\Controller
 */
class EO2OBaseController extends BaseController {
	function _initialize() {
		parent::_initialize();
		
        $action = strtolower ( _ACTION );
		$res ['title'] = '系统设置';
		$res ['url'] = addons_url ( 'EO2OPayment://EO2OPayment/config' );
		$res ['class'] = $action == 'config' ? 'current' : '';
		$nav [] = $res;

        $res ['title'] = '划款信息';
        $res ['url'] = addons_url ( 'EO2OPayment://EO2OPayment/lists' );
        $res ['class'] = $action == 'lists' ? 'current' : '';
        $nav [] = $res;


        $this->assign ( 'nav', $nav );
		
		$config = getAddonConfig ( 'EO2OPayment' );
		$config ['background_url'] = $config ['background'] == 11 ? $config ['background_custom'] : ADDON_PUBLIC_PATH . '/card_bg_' . $config ['background'] . '.png';
		$this->assign ( 'config', $config );
	}
	
	protected function log($logtxt)
	{
		$time = date('Y-m-d H:i:s',time());
		
		file_put_contents('log.txt', $time.':'.$logtxt.PHP_EOL,FILE_APPEND);
	}
}