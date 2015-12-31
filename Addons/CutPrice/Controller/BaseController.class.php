<?php

namespace Addons\CutPrice\Controller;


/**
 * Class BaseController
 * @package Addons\CutPrice\Controller
 */
class BaseController extends \Addons\School\Controller\BaseController {
    // config
	var $config;

    /**
     * init
     */
	function _initialize() {
		parent::_initialize();

        $action = strtolower ( _ACTION );
		$res ['title'] = '砍价统计';
        $res ['url'] = addons_url ( 'CutPrice://CutPrice/lists' );
        $res ['class'] = $action == 'lists' ? 'current' : '';
        $nav [] = $res;

//        $res ['title'] = '砍价明细';
//        $res ['url'] = addons_url ( 'CutPrice://CutPrice/listsDetail' );
//        $res ['class'] = $action == 'listsdetail' ? 'current' : '';
//        $nav [] = $res;

        $res ['title'] = '系统设置';
        $res ['url'] = addons_url ( 'CutPrice://CutPrice/config' );
        $res ['class'] = $action == 'config' ? 'current' : '';
        $nav [] = $res;

        $this->assign ( 'nav', $nav );
		
		$config = getAddonConfig ( 'CutPrice' );
		$config ['cover_url'] = get_cover_url ( $config ['cover'] );
		$this->config = $config;
		$this->assign ( 'config', $config );
	}
}
