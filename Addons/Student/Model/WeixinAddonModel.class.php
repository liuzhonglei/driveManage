<?php
        	
namespace Addons\Student\Model;
use Home\Model\WeixinModel;
        	
/**
 * Student的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Student' ); // 获取后台插件的配置参数	
		//dump($config);

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
        if($result[0] == 'success'){
            $info['noti_result'] = '2';
            $info['remark'] = '学员已接收通知。';
        }else{
            $info['noti_result'] = '-1';
            $info['remark'] = '学员接受通知失败原因为['.$result[1].']。';
        }
        $Model->save($info);
    }
}
        	