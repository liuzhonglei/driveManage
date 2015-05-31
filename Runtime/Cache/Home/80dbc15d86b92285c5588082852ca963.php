<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link rel="apple-touch-icon-precomposed" href="img/screen_icon.png">
<title>car</title>
<link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/jquery.mobile-1.4.4.css" />
<link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/car.css">
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery-1.11.1.min.js"></script>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/iscroll.js"></script>
<script>
/*滚动条*/    
var myScroll;
function loaded () {
    /*iscroll*/ 
	myScroll = new IScroll('#c_content', {
        tap:true,
		click:false,
		preventDefaultException:{tagName:/.*/},//解除默认事件锁定，调用tab，click等事件,不支持swipeleft
		scrollbars: true,
		mouseWheel: true,
		fadeScrollbars: true
	}); 
	
}

function preventDefault(e) { e.preventDefault(); };
document.addEventListener('touchmove', preventDefault, false);
</script>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery.mobile-1.4.4.min.js"></script>
</head>
<body onload="loaded()">
<div id="pageNav" data-role="page" data-theme="none">
    <div data-role="header" data-theme="none"  class="x_header"  data-position="fixed"  data-tap-toggle="false">
        <a href="#"  data-role="none" data-rel="back" class="x_btn_back" data-transition="slide"></a>
        <h1>校区导航</h1>
    </div><!--//header -->
    <div id="c_content" class="c_wrapper">
        <div id="scroller">
            <div class="c_padd">
        		<!--校区导航-->
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="c_navigation">
                	<div class="row01">
                    	<div class="pic01">
                    		<img src="<?php echo ($vo["path"]); ?>">
                        </div>
                        <div class="info01">
                    		<div class="tit01"><?php echo ($vo["name"]); ?></div>
                            <dl>
                            	<dt>门店地址：</dt>
                                <dd><?php echo ($vo["address"]); ?></dd>
                            </dl>
                            <dl>
                            	<dt>门店电话：</dt>
                                <dd><?php echo ($vo["phone"]); ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="callfor">
                    	<ul>
                        	<a href="tel:<?php echo ($vo["phone"]); ?>"><li><img src="<?php echo ADDON_PUBLIC_PATH;?>/img/icon_telephone.png">电话咨询</li></a>
                            <a href="<?php echo ($vo["map_url"]); ?>" data-ajax="false"><li><img src="<?php echo ADDON_PUBLIC_PATH;?>/img/icon_map.png">线路导航</li>
                        </ul>
                    </div>
                </div>
                <div class="x_blank2"></div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
	</div><!-- /wrapper -->
</div> <!--//Page-->
</body>
</html>