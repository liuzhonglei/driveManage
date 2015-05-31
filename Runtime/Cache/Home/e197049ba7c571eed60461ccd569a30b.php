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
<link rel="apple-touch-icon-precomposed" href="img/screen_icon.png">
<title>car</title>
<link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/jquery.mobile-1.4.4.css"/>
<link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/car.css">
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery-1.11.1.min.js"></script>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/iscroll.js"></script>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery.mobile-1.4.4.min.js"></script>
<script>
    var myScroll_ask;
    function loaded() {
        /*iscroll*/
        myScroll_ask = new IScroll('#c_content', {
            tap: true,
            click: false,
            preventDefaultException: {tagName: /.*/},//解除默认事件锁定，调用tab，click等事件,不支持swipeleft
            scrollbars: true,
            mouseWheel: true,
            fadeScrollbars: true
        });
    }
</script>

<link href="<?php echo ADDON_PUBLIC_PATH;?>/css/privilege.detail.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<body id="weisite">
<div class="container">
    <div data-role="header" data-theme="none" class="x_header" data-position="fixed" data-tap-toggle="false">
        <a href="#" data-role="none" data-rel="back" class="x_btn_back" data-transition="slide"></a>

        <h1>最新优惠</h1>
    </div>
    <!--//header -->
    <!--<div id="c_content" class="c_wrapper">-->
        <!--<div id="scroller">-->
            <div class="detail">
                <h6 class="title"><?php echo ($info["title"]); ?></h6>
                <p class="info"><span class="colorless"><?php echo (time_format($info["cTime"])); ?></span></p>
                <section class="content">
                    <?php if(!empty($info["cover"])): ?><p><img src="<?php echo (get_cover_url($info["cover"])); ?>"/></p><?php endif; ?>
                    <?php echo (htmlspecialchars_decode($info["content"])); ?>
                </section>
            </div>
        <!--</div>-->
    <!--</div>-->
</div>
<!-- 底部导航 -->
<?php echo ($footer_html); ?>
<!-- 统计代码 -->
<?php if(!empty($config["code"])): ?><p class="hide bdtongji">
        <?php echo ($config["code"]); ?>
    </p><?php endif; ?>
</body>
</html>