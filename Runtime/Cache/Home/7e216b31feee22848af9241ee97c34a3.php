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
    	<div class="top"></div>
    	<div class="lead_box">
        	<h6><?php echo ($info["title"]); ?></h6>
            <div class="lead_content"><?php echo ($info["intro"]); ?>
            <?php if(!empty($info["start_time"])): ?><p>考试开始时间：<?php echo (time_format($info["start_time"])); ?></p><?php endif; ?>
            <?php if(!empty($info["end_time"])): ?><p>考试结束时间：<?php echo (time_format($info["end_time"])); ?></p><?php endif; ?>
            <?php if(!empty($score)): ?><p>&nbsp;</p><p>你上次的成绩为：<?php echo ($score); ?>分</p><?php endif; ?>
            </div>
            
            <a href="javascript:void(0)" onClick="begin()" class="lead_btn">马上开始</a>
        </div>
        <div class="bottom"></div>
        <p class="copyright"><?php echo ($system_copy_right); ?></p>
  	</div>  
</body>
</html>
<script type="text/javascript">
function begin(){
	var start = <?php echo (intval($info["start_time"])); ?>;
	var end = <?php echo (intval($info["end_time"])); ?>;
	var now = <?php echo time();?>;
	if(start>0 && now<start){
		$.Dialog.fail('考试时间还没到，请稍等');
		return false;
	}
	if(end>0 && now>end){
		$.Dialog.fail('考试已经结束，你来晚了');
		return false;
	}
	
	window.location.href="<?php echo U('profile','exam_id='.$info[id]);?>";
}
</script>