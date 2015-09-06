/**
 * Created by wuhanchu on 15/8/27.
 */

var _hmt = _hmt || [];
(function () {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?60df510c62e6efbf250f64fa290d6ff5";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();

function handleerror(msg,url,line){
    alert("error:\nmsg:"+msg+"\nurl:"+url+"line:"+line);
    return true;
  };
//window.onerror = handleerror;

function getRequestParam(param) {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("/");
        for (var i = 0; i < strs.length; i++) {
           if(strs[i] == param){
                return strs[i+1];
           }
        }
    }
    return null;
}