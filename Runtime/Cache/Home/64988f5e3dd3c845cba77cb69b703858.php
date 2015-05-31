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
<link href="<?php echo ADDON_PUBLIC_PATH;?>/Coupon.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<body id="scratch">
	<div class="container body" style="position:relative">
    	<div class="prev_get">
        	<div class="top"></div>
            <div class="mid">
                <?php if(!empty($my_sn_list)): ?><div class="get_text">
                	<h6>已获得的优惠券</h6>
                    <p class="list">
                        <?php if(is_array($my_sn_list)): $i = 0; $__LIST__ = $my_sn_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sn): $mod = ($i % 2 );++$i;?>SN码&nbsp;&nbsp;：<?php echo ($sn["sn"]); ?><br/>
                状 态&nbsp;&nbsp;：<?php if(($sn["is_use"]) == "1"): ?>已使用<?php else: ?>未使用<?php endif; ?><br/>
                
                <?php if(!empty($data['start_time']) && !empty($data['end_time'])): ?>有效期：<?php echo (time_format($data["start_time"])); ?> 至 <?php echo (time_format($data["end_time"])); endif; ?>

                <?php if(!empty($data['start_time']) && empty($data['end_time'])): ?>开始时间：<?php echo (time_format($data["start_time"])); endif; ?>
                <?php if(empty($data['start_time']) && !empty($data['end_time'])): ?>截止时间：<?php echo (time_format($data["end_time"])); endif; ?>  
                                
                <br/><?php endforeach; endif; else: echo "" ;endif; ?>    
                          	
                    </p>
                </div><?php endif; ?>
                
                <div class="get_text">
                	<h6><?php echo ($data["title"]); ?></h6>
                    <p>简介： <?php echo ($data["intro"]); ?></p>
                </div>                
                
                <div class="get_text">
                	<h6>领取条件</h6>
                    <p class="list">
                        <?php if(is_array($condition)): $i = 0; $__LIST__ = $condition;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span class="num"><?php echo ($key+1); ?></span><?php echo ($vo); ?> <br/><?php endforeach; endif; else: echo "" ;endif; ?>    
                          	
                    </p>
                </div>
                <?php if(!empty($error)): ?><h3 class="get_error">
                        <?php echo ($error); ?>
                    </h3>
                <?php else: ?>
            		<center><a href="<?php echo U('set_sn_code',array('id'=>$data[id]));?>"><img class="get_btn" src="<?php echo ADDON_PUBLIC_PATH;?>/get_btn.jpg"/></a></center><?php endif; ?>
                
            </div>
            <div class="btm"></div>
        </div>
        
        <p class="copyright"><?php echo ($system_copy_right); ?></p>
        </div>
    </div>
</body>
<script type="text/javascript">
var h=$(document).height();
$('.container').css("min-height",h);
</script>
</html>