<?php

namespace Addons\EO2OPayment\Controller;
use Home\Controller\AddonsController;

include_once "WxPayPubHelper.php";

class EO2OPaymentController extends BaseController{

	public function Payment()
	{
		$appinfo = get_token_appinfo();
		$config = getAddonConfig ( 'EO2OPayment' );
		$wxconfig = array(
		 'APPID' => $appinfo['appid'],
		 'MCHID' => $config['MCHID'],
		 'KEY' =>$config['KEY'],
		 'APPSECRET' => $appinfo['secret'],
		 'SSLCERT_PATH' =>$config['SSLCERT_PATH'],
		 'SSLKEY_PATH' =>$config['SSLKEY_PATH'],
		 'NOTIFY_URL' => $config['NOTIFY_URL'],
		 'CURL_TIMEOUT' => 30		
		);
		
		//使用jsapi接口
		$jsApi = new \JsApi_pub($wxconfig);
		//使用统一支付接口
		$unifiedOrder = new \UnifiedOrder_pub($wxconfig);
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("openid",get_openid());
		$unifiedOrder->setParameter("body",I('desc'));//商品描述
						
		if(I('out_trade_no') != null && I('out_trade_no')!="")
			$unifiedOrder->setParameter("out_trade_no",I('out_trade_no'));//商户订单号 
		else
		{
			$timeStamp = time();
			$out_trade_no = $appinfo['appid']."$timeStamp";
			$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		}
		
		$total = I("total");
		$total = $total * 100;
		$unifiedOrder->setParameter("total_fee",$total);//总金额
		
		$url = U('Notify',array("token"=>get_token(),"openid"=>get_openid()));
		$url = str_replace("?s=","", $url);
		$unifiedOrder->setParameter("notify_url",$url);//默认通知地址
		
		if(!isset($_GET['notify_url']))
		{
			if($config['NOTIFY_URL']!=null && $config['NOTIFY_URL']!="")
			{
				$unifiedOrder->setParameter("attach",urlencode($config['NOTIFY_URL']));//设置的通知地址 
			}
		}
		else
			$unifiedOrder->setParameter("attach",I('notify_url'));//自定义通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		
		$prepay_id = $unifiedOrder->getPrepayId();

		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);

		$jsApiParameters = $jsApi->getParameters();

		$this->assign("total",I("total"));
		$this->assign("desc",I("desc"));
		$this->assign("jsApiParameters",$jsApiParameters);
		$this->display();

	}
	public function Notify()
	{
		$appinfo = get_token_appinfo();
		$config = getAddonConfig ( 'EO2OPayment' );
		$wxconfig = array(
		 'APPID' => $appinfo['appid'],
		 'MCHID' => $config['MCHID'],
		 'KEY' =>$config['KEY'],
		 'APPSECRET' => $appinfo['secret'],
		 'SSLCERT_PATH' =>$config['SSLCERT_PATH'],
		 'SSLKEY_PATH' =>$config['SSLKEY_PATH'],
		 'NOTIFY_URL' => $config['NOTIFY_URL'],
		 'CURL_TIMEOUT' => 30		
		);
			//使用通用通知接口
		$notify = new \Notify_pub($wxconfig);

		//存储微信的回调
		$xml = file_get_contents("php://input");
		$notify->saveData($xml);
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		
		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
		if($notify->checkSign() == TRUE)
		{
			$xml = $notify->getData();

			$xml['token'] = get_token();
			 M('eo2o_payment')->add($xml);

			$url = addons_url(urldecode($xml['attach']),$xml);
			file_get_contents($url);
		}
	}
	public function PayTo()
	{
		if(IS_POST)
		{
			$map['total'] = I('total');
			$map['desc'] = I('desc');
			$map['notify_url'] = urlencode('EO2OPayment://EO2OPayment/CallBackLog');//回调插件名称 可选参数
			$map['out_trade_no'] = get_token().time();//可选参数，用户自己的订单编号
			$map['token'] = get_token();//可选参数，weiphp系统内跳转无需传入
			$map['openid'] = get_openid();//可选参数，weiphp系统内跳转无需传入
			$url = addons_url ( 'EO2OPayment://EO2OPayment/Payment', $map );
			header('Location:'.$url);
		}
		else
		{
			$this->display();
		}
		
	}
	//https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7 参数参考
	public function CallBackLog()
	{
		$this->log(I('out_trade_no'));
		$this->log(I('result_code'));
	}
}
