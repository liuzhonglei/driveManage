/**
 * the call pay method
 */
function callpay(payParameter,callback) {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall(payParameter,callback);
    }
}
function jsApiCall(payParameter,callback) {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        JSON.parse(payParameter),
        function (res) {
            WeixinJSBridge.log(res.err_msg);
            if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                if(callback != null){
                    callback();
                }
            }else if(res.err_msg == "get_brand_wcpay_request:fail" ){
                alert("支付失败");
            }
        }
    );
}
