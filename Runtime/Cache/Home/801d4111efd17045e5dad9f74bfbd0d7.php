<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <meta name="apple-mobile-web-app-capable" content="yes" />

    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <meta name="format-detection" content="telephone=no">

    <link rel="apple-touch-icon-precomposed" href="<?php echo ADDON_PUBLIC_PATH;?>/img/screen_icon.png">

    <title>car</title>

    <link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/jquery.mobile-1.4.4.css" />

    <link rel="stylesheet" href="<?php echo ADDON_PUBLIC_PATH;?>/css/car.css">

    <script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery-1.11.1.min.js"></script>


    <script>
        /*滚动条*/
        $(function(){
            /*报名表单*/
            $(".c_formFull input").focus(function(){
                $(this).addClass("focus");

                if($(this).val() ==this.defaultValue){

                    if($(this).attr("id")=="psw"){

                        $("#pswShow").show();

                        $(this).attr("type","password");

                    }

                    $(this).val("");

                }

            }).blur(function(){

                if ($(this).val() == '') {

                    $(this).removeClass("focus");

                    $(this).val(this.defaultValue);

                    if($(this).attr("id")=="psw"){

                        $(this).attr("type","text");



                    }

                }else{

                    $(this).addClass("focus");

                }
            });
        });

    </script>

    <script src="<?php echo ADDON_PUBLIC_PATH;?>/js/jquery.mobile-1.4.4.min.js"></script>

</head>

<body class="x_flowAuto">

<div id="pageRegGuide" data-role="page" data-theme="none">

    <div data-role="header" data-theme="none"  class="x_header"  data-position="fixed"  data-tap-toggle="false">

        <a href="<?php echo ADDON_PUBLIC_PATH;?>/index.html"  data-role="none" data-rel="back" data-ajax="false" class="x_btn_back"></a>

        <h1>在线报名</h1>

    </div><!--//header -->

    <div class="x_tabs c_fixed" id="c_tabs_guide" >

        <ul>

            <li class="active"><a href="#">报名须知</a></li>

            <li><a href="#">学车流程</a></li>

            <li><a href="#">在线报名</a></li>

        </ul>

        <div class="x_tabs_slide"><span id="slide"></span></div>

    </div><!--//tabs -->

    <div class="x_tab_content" style="background:#fff;padding:40px 0 0">
        <div class="conBk"   style="width:300%; position:relative; top:0; left:0;">

            <div class="con" style="width:33.33%;background:#fff;">

                <div id="xuzhi">

                    <!--报名须知-->

                    <div class="c_article">

                        <?php echo ($sign_info); ?>

                    </div>

                </div>

            </div>

            <div class="con" style="width: 33.33%;background:#fff;">

                <div id="liuchen">

                    <!--学车流程-->

                    <div class="c_article">

                        <?php echo ($learn_flow); ?>

                    </div>

                </div>

            </div>

            <div class="con" style="width: 33.33%;">

                <div id="baoming">

                    <!--在线报名-->

                    <div class="x_blank"></div>

                    <div class="x_item_tit">预约方式</div>

                    <div class="x_item">

                        <ul>

                            <a href="tel:<?php echo ($data["phone"]); ?>"><li><img src="<?php echo ADDON_PUBLIC_PATH;?>/img/icon_phone.png"><span>预约电话：<?php echo ($data["phone"]); ?></span></li></a>

                            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo ($data["qq"]); ?>&amp;site=qq&amp;menu=yes"><li><img src="<?php echo ADDON_PUBLIC_PATH;?>/img/icon_qq2.png"><span>联系QQ：<?php echo ($data["qq"]); ?></span></li></a>

                            <li class="noLink"><img src="<?php echo ADDON_PUBLIC_PATH;?>/img/icon_user.png"><span>联系人：<?php echo ($data["contact"]); ?></span></li>

                        </ul>

                    </div>



                    <div class="x_blank"></div>

                    <div class="x_item_tit">填写报名信息</div>

                    <div class="c_formFull">

                        <input id = "re_name" type="text" data-role="none" value="请输入您的姓名">

                        <input id = "re_phone" type="text" data-role="none" value="请输入电话号码">

                        <input id = "re_remark" type="text" data-role="none" value="备注">

                    </div>

                    <div class="c_remark">报名说明：报名成功后,驾校会在第一时间联系您与您确认,请耐心等候。如急需报名可拨打我们的预约电话联系我们。</div>

                    <div class="c_warn_bk" >

                        <div class="c_warn" id="c_warn" style="display:none"><span class="icon_warn"></span><label id="c_warn_label">异常消息提示</label></div>

                    </div>

                    <div class="x_btn_bk" >

                        <a href="#" class="ui-btn ui-corner-all x_btn x_btn_curr" onclick = "info_submit()">提交</a>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <script type="text/javascript">



        $(function(){



            /*如果在首页点击的是在线报名，则执行下面三行*/

            <?php
 if ($section <> 0) { echo '$(".x_tabs li").eq(0).removeClass("active").eq(2).addClass("active");'; echo '$("#slide").css("left","66.67%");'; echo '$(".conBk").css("left","-200%");'; }else{ } ?>










            /*-----滑动-左-----*/

            $(".x_tab_content").on( "swipeleft", function( event ) {

                event.stopPropagation();  //阻止冒泡

                if(tabN==1){

                    $("#slide").animate({ left :'33.33%' }, 300); //滑动到n2





                }

                if(tabN==2){

                    $("#slide").animate({ left :'66.67%' }, 300); //滑动到n3



                }



                if( !conObj.is(":animated") ){

                    if( tabN == 3 ){

                        return false;

                    }else{

                        conObj.animate({ left : -(v_width * tabN)+'%' }, 300)

                        tabN++;

                    }

                }

                $("#c_tabs_guide li").eq(tabN-1).addClass("active").siblings().removeClass("active");

            });



            /*-----滑动-右-----*/

            $(".x_tab_content").on( "swiperight", function( event ) {

                event.stopPropagation(); //阻止冒泡

                if(tabN==3){

                    $("#slide").animate({ left :'33.33%' }, 300); //滑动到n2



                }

                if(tabN==2){

                    $("#slide").animate({ left :'0%' }, 300); //滑动到n1




                }

                if( !conObj.is(":animated") ){

                    if( tabN == 1 ){

                        return false;

                    }else{

                        var numr=parseInt(tabN-2);

                        conObj.animate({ left : -(v_width * numr)+'%' }, 300)

                        tabN--;

                    }

                }

                $("#c_tabs_guide li").eq(tabN-1).addClass("active").siblings().removeClass("active");

            });



            /*-----选项卡-------*/

            var tabN=3;

            var v_width="100";

            var conObj= $(".conBk");

            $("#c_tabs_guide li").each(function(i){

                $(this).click(function(){

                    if(i==0){

                        conObj.animate({ left : 0 },300);

                        $("#slide").animate({ left :'0%' }, 300); //滑动n1





                    }

                    if(i==1){

                        conObj.animate({ left : -(v_width * i)+'%' }, 300);

                        $("#slide").animate({ left :'33.33%' }, 300); //滑动到n2




                    }

                    if(i==2){

                        conObj.animate({ left : -(v_width * i)+'%' }, 300);

                        $("#slide").animate({ left :'66.67%' }, 300); //滑动到n3


                    }

                    $(this).addClass("active").siblings().removeClass("active");

                    tabN=i+1;

                })

            })



        });





        function info_submit() {

            var name = $("#re_name").val();

            var phone = $("#re_phone").val();

            var remark = $("#re_remark").val();

            $.ajax({ //一个Ajax过程

                type: "post",

                url: "<?php echo U('register');?>",

                dataType: 'json',

                data: 'name=' + name + '&phone=' + phone + '&remark=' + remark,

                success: function (data) {

                    if(data.status == 0 ){

                        document.getElementById("c_warn").style.display = "";

                        document.getElementById("c_warn_label").innerHTML = data.info;

                    }else{

                        alert("报名成功");

                    }

                },

                failed: function(){

                    alert("报名出错！");

                }

            })

        };





    </script>

</div> <!--//Page-->

</body>

</html>