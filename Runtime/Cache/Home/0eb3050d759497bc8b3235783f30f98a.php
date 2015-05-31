<?php if (!defined('THINK_PATH')) exit();?><meta charset="utf-8">
<div class="c_box">
	<dl class="mainInfo">
    	<dt>基本资料：</dt>
        <dd><?php echo ($teacher_data["intro"]); ?></dd>
        <!--<dd>32岁，男，从事汽车教练工作10年，有教练资格证书，为人友善，做事认真细心，是学员公认的好教练，荣获省交通颁发的"十佳教练"称号。</dd>-->
    </dl>
	<dl class="mainInfo">
    	<dt>教学范围：</dt>
        <dd>
            <?php echo ($teacher_data["course_names"]); ?>

        </dd>
    </dl>
	<dl class="mainInfo">
    	<dt>教学区域：</dt>
        <dd><?php echo ($teacher_data["area"]); ?></dd>
    </dl>
</div>
<div class="x_blank"></div>
<div class="c_box">
	<div class="tit01">开设课程</div>
    <div class="mainList">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
            <?php if(is_array($course_data)): $i = 0; $__LIST__ = $course_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td width="40%"><?php echo ($vo["name"]); ?></td>
            <td width="50%"><?php echo ($vo["sign_charge"]); ?>元 </td>
            <td><div class="space01"><a href="#" data-role="none" class="c_btn" id="reserve<?php echo ($vo["id"]); ?>">预约</a></div></td>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>
          <!--<tr>-->
            <!--<td width="40%">C2培训班</td>-->
            <!--<td width="50%">6000元 </td>-->
            <!--<td><div class="space01"><a href="#" data-role="none" class="c_btn" >预约</a></div></td>-->
          <!--</tr>-->
          <!--<tr>-->
            <!--<td width="40%">B2培训班</td>-->
            <!--<td width="50%">8000元 </td>-->
            <!--<td><div class="space01"><a href="#" data-role="none" class="c_btn">预约</a></div></td>-->
          <!--</tr>-->
        </table>
    </div>
</div>
<div class="x_blank"></div>
<div class="x_item subItem">
    <ul>
        <li>锦旗数量(8)</li>
    </ul>
</div>
<div class="x_blank"></div>
<div class="c_box">
	<div class="tit01">学员评价（<?php echo sizeof($apprise_data);?>）</div>
	<div class="masterComment">
    	<ul>
            <?php if(is_array($apprise_data)): $i = 0; $__LIST__ = $apprise_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
            	<div class="tit01"><?php echo ($vo["show_name"]); ?><span><?php echo ($vo["format_time"]); ?></span></div>
                <div class="row01">
                    <?php echo ($vo["apprise"]); ?>
                </div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        	<!--<li>-->
            	<!--<div class="tit01">王**<span>2015-05-15  12:10</span></div>-->
                <!--<div class="row01">-->
                    <!--科目二时候安排合理，给我充分的实践时间，一个月时间我就过了，太感谢-->
                <!--</div>-->
            <!--</li>-->
        	<!--<li>-->
            	<!--<div class="tit01">李**<span>2015-05-13  11:20</span></div>-->
                <!--<div class="row01">-->
                    <!--感谢，李教练，上课经验丰富，理论和实践的完美结合是我收益匪浅-->
                <!--</div>-->
            <!--</li>-->
        </ul>
    </div>
</div>
<script>
$(function(){
	<!--点击预约-->
    <?php if(is_array($course_data)): $i = 0; $__LIST__ = $course_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>$("#reserve<?php echo ($vo["id"]); ?>").click(function(){
		$.ajax({  
			type:"get",  
			url:"<?php echo U('getTeacherReg');?>",
			dataType:"html",
            data:"id=<?php echo ($vo["id"]); ?>&name=<?php echo ($vo["name"]); ?>&sign_charge=<?php echo ($vo["sign_charge"]); ?>",
			success:function(data){  
				$('#masterCon').html(data);
			} 
       });
	   $('#masterNav li').eq(2).addClass("active").siblings().removeClass("active")	
	});<?php endforeach; endif; else: echo "" ;endif; ?>
})
</script>