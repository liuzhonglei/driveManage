<?php

namespace Addons\CutPrice\Controller;

/**
 * Class CutPriceCountController
 * @package Addons\CutPrice\Controller
 */
class CutPriceCountController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'custom_reply_text' );
		parent::_initialize ();
	}
}
