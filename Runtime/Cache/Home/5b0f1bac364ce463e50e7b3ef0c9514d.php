<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/font-awesome.css?v=<?php echo SITE_VERSION;?>" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Home/css/mobile_module.css?v=<?php echo SITE_VERSION;?>" media="all">
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/prefixfree.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/m/dialog.js?v=<?php echo SITE_VERSION;?>"></script>
    <script type="text/javascript" src="/Public/Home/js/m/flipsnap.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/m/mobile_module.js?v=<?php echo SITE_VERSION;?>"></script>
    <script type="text/javascript" src="/Public/Home/js/admin_common.js?v=<?php echo SITE_VERSION;?>"></script>
	<title><?php echo empty($page_title) ? C('WEB_SITE_TITLE') : $page_title; ?></title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
    <meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
    <meta content="no-cache" http-equiv="pragma">
    <meta content="0" http-equiv="expires">
    <meta content="telephone=no, address=no" name="format-detection">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="shortcut icon" href="<?php echo SITE_URL;?>/favicon.ico">
</head>	
<link href="<?php echo ADDON_PUBLIC_PATH;?>/css.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<body>
	<div id="container" class="container">
    	<div class="lead_box">
        	<h6>请填写你的资料</h6>
            <form id="form" action="<?php echo U('profile?survey_id='.$_GET['survey_id']);?>" method="post">
            <div class="lead_content">            
            	<div class="form-item cf">
                  	<label class="item-label">填写你的名称</label>
                    <div class="controls">
                     <input type="text" placeholder="输入你的名称" class="text input-medium" name="nickname" value="<?php echo ($info["nickname"]); ?>">
                    </div>
                </div>
                <div class="form-item cf">
                    <label class="item-label">输入你的手机号<span class="check-tips"></span></label>
                    <div class="controls">
                     <input type="tel" placeholder="输入你的手机号" class="text input-medium" name="mobile" value="<?php echo ($info["mobile"]); ?>">
                    </div>
                </div>            
            </div>
            <input class="lead_btn" id="submit" type="submit" value="开始调研">
            </form>
	</div>
    <p class="copyright"><?php echo ($system_copy_right); ?></p>
    <script type="text/javascript">
$('#submit').click(function(){
   // $('#form').submit();
   //判断
   if($('input[name="nickname"]').size()>0 && ( $('input[name="nickname"]').val().length<2 || !(/^[\u4e00-\u9fa5]+$/.test($('input[name="nickname"]').val())))){
   		 $.Dialog.fail("名字限2个以上的中文字符!");
		 return false;
   }
   if($('input[name="mobile"]').size()>0 && $('input[name="mobile"]').val().length!=11){
   		 $.Dialog.fail("请填写正确的手机号码!");
		 return false;
   }
   
});
</script>
</body>
</html>