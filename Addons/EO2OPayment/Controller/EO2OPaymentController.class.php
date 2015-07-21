<?php

namespace Addons\EO2OPayment\Controller;
use Home\Controller\AddonsController;
include_once "WxPayPubHelper.php";

class EO2OPaymentController extends BaseController{
    // the pay type info
    private static $paytypeInfo = array('banner' => array('page'=>'Addons://EO2OPayment@EO2OPayment/Payment','callback'=>'EO2OPayment://EO2OPayment/CallBackLog'));

    /**
     * start pay request
     */
    public function Payment()
    {
        // param
        $appinfo = get_token_appinfo();
        $payType = i('paytype');
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

        // 请求预支付ID
        $unifiedOrder = new \UnifiedOrder_pub($wxconfig);
        $unifiedOrder->setParameter("openid",get_openid());
        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        $unifiedOrder->setParameter("body",I('desc'));//商品描述
        $unifiedOrder->setParameter("attach",i('attach'));//附加数据

        //订单号
        $timeStamp = time();
        $out_trade_no = $appinfo['appid']."$timeStamp";
        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号

        //总金额
        $total = I("total");
        $total = $total * 100;
        $unifiedOrder->setParameter("total_fee",$total);

        // notify url
        $url = U('Notify',array("token"=>get_token(),"openid"=>get_openid()));
        $url = str_replace("?s=","", $url);
        $unifiedOrder->setParameter("notify_url",$url);//默认通知地址

        // get the $prepay_id
        $prepay_id = $unifiedOrder->getPrepayId();
        $transaction = array();
        $transaction = array_merge($unifiedOrder->parameters,$unifiedOrder->result);
        $transaction['token'] = get_token();
        $transaction['time_begin'] = time();
        $Model = M('eo2o_payment');
        $Model->add($transaction);
        if(!empty($Model->getError())){
            $this->error($Model->getError());
        }

        if(!$prepay_id){
            $this->error($unifiedOrder->result['return_msg']);
        }




        //使用jsapi接口,返回请求支付
        $jsApi = new \JsApi_pub($wxconfig);
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        $this->assign("total",I("total"));
        $this->assign("desc",I("desc"));
        $this->assign("jsApiParameters",$jsApiParameters);
        $pageUrl = T(self::$paytypeInfo[$payType]['page']);
        $this->display($pageUrl);
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
            // get the data
            $xml = $notify->getData();
            $xml['token'] = get_token();

            //update the transaction data
            $model =  M('eo2o_payment');
            $transaction = $model->where('out_trade_no="'.$xml['out_trade_no'].'"');
            if($transaction){
                $model->where('id='.$transaction['id'].'')->save($xml);
            }else{
                $model->add($xml);
            }

            // invoke the moudle method
            $url = addons_url(self::$paytypeInfo[$transaction['payType']]['callback'],$xml);
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
