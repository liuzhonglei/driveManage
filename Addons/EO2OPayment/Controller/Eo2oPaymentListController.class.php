<?php

namespace Addons\EO2OPayment\Controller;
use Home\Controller\AddonsController;

class Eo2oPaymentListController extends BaseController{

	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'eo2o_payment' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$this->assign ( 'add_button', false );		
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $this->model );
		foreach($list_data['list_data'] as &$vo)
		{
			$total = (float)$vo['total_fee'];
			$vo['total_fee'] ='￥'.($total/100).'元';
		}
		
		$this->assign ( $list_data );
		//dump ( $list_data );
		
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
