<?php
        	
namespace Addons\Teacher\Model;
use Home\Model\WeixinModel;
        	
/**
 * Teacher的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
        // 其中token和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
        $param ['token'] = $this->data['ToUserName'];
        $param ['openid'] =$this->data['FromUserName'];

        // get the data
        $map = array('token' => $param ['token'],'type'=>'0');
        $school = M('school')->where($map)->find();
        $photo = M('school_photo')->where($map)->order("rand()")->find();

        // 组装微信需要的图文数据，格式是固定的
        $articles [0] = array(
            'Title' => $school['name'],
            'Description' => '点击查找教练',
            'PicUrl' => get_cover_url($photo['photo']),
            'Url' =>  addons_url('School://School/getTeacher', $param)
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
}
        	