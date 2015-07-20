<?php
        	
namespace Addons\EO2OPayment\Model;
use Home\Model\WeixinModel;
        	
/**
 * EO2OPayment的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'EO2OPayment' ); // 获取后台插件的配置参数	
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		$url = addons_url ( 'EO2OPayment://EO2OPayment/PayTo', $param );
			// 组装微信需要的图文数据，格式是固定的
			$articles [0] = array (
					'Title' => 'E客邦自定义支付',
					'Description' => '自定义金额支付',
					'PicUrl' => '',
					'Url' => $url
			);
	$this->replyNews ( $articles );

	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	