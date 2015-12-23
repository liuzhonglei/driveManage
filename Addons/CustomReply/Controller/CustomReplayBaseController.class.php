<?php

namespace Addons\CustomReply\Controller;

use Home\Controller\AddonsController;
use Addons\School\Controller\BaseController;

class CustomReplayBaseController extends BaseController {
	var $config;
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '图文回复';
		$res ['url'] = addons_url ( 'CustomReply://CustomReply/lists' );
		$res ['class'] = $controller == 'customreply' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '多图文设置';
		$res ['url'] = addons_url ( 'CustomReply://CustomReplyMult/lists' );
		$res ['class'] = $controller == 'customreplymult' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '文本回复';
		$res ['url'] = addons_url ( 'CustomReply://CustomReplyText/lists' );
		$res ['class'] = $controller == 'customreplytext' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
		$config = getAddonConfig ( 'CustomReply' );
		$config ['cover_url'] = get_cover_url ( $config ['cover'] );
		$this->config = $config;
		$this->assign ( 'config', $config );
		// dump ( $config );
		// dump(get_token());
	}
	// 重写的保存关键词方法
	public function _saveKeyword($model, $id, $extra_text) {
		D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], $extra_text );
	}

	/**
	 * 删除配置,增加删除关键字
	 * @param null $model
	 * @param null $ids
	 */
	public function common_del($model = null, $ids = null) {

		is_array ( $model ) || $model = $this->getModel ( $model );

		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

		$map ['id'] = array (
				'in',
				$ids
		);

		if(!is_array($ids)){
			D ( 'Common/Keyword' )->del (_ADDONS, $ids);
		}
		foreach($ids as $id){
			D ( 'Common/Keyword' )->del (_ADDONS, $id);
		}

		parent::common_del ( $this->model );
	}
}
