/**
 * the call pay method
 */
function callpay(payParameter) {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall(payParameter);
    }
}
function jsApiCall(payParameter) {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        JSON.parse(payParameter),
        function (res) {
            WeixinJSBridge.log(res.err_msg);
        }
    );
}