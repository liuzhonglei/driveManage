<?php
        	
namespace Addons\Student\Model;
use Home\Model\WeixinModel;
        	
/**
 * Student的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Student' ); // 获取后台插件的配置参数	
        // 其中token和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
        $param ['token'] = $this->data['ToUserName'];
        $param ['openid'] =$this->data['FromUserName'];

        // get the data
        $map = array('token' => $param ['token'],'type'=>'0');
        $school = M('school')->where($map)->find();
        $photo = M('school_photo')->where($map)->find();

        // 组装微信需要的图文数据，格式是固定的
        $articles [0] = array(
            'Title' => $school['name'],
            'Description' => '点击进入个人中心',
            'PicUrl' => get_cover_url($photo['photo']),
            'Url' =>  addons_url('School://School/getMyInfo', $param)
        );
        $this->replyNews($articles);

	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}

    public function templatesendjobfinish($data){
        addWeixinLog ( $data,'templatesendjobfinish' );

        $Model =  M('student_notification');
        $result = explode(':',$data['Status']);
        $info = $Model->where('msgid="'.$data['MsgID'].'"')->find();
        if($result[0] == 'success' && $info['noti_result'] != '2'){
            $info['noti_result'] = '2';
            $info['remark'] = '学员已接收通知。';
        }else{
            $info['noti_result'] = '-1';
            $info['remark'] = '学员接受通知失败原因为['.$result[1].']。';
        }
        $Model->save($info);
    }
}
        	