<?php

namespace Addons\Teacher\Controller;
use Addons\Student\Controller\StudentController;
use Home\Controller\AddonsController;
use Addons\School\Controller\BaseController;
use Think\Model;

class WeixinController extends BaseController{
	function _initialize() {
        $this->model = $this->getModel ( 'follow' );
        parent::_initialize ();

        // 子导航
        $action = strtolower ( _ACTION );
        $res ['title'] = '信息';
        $res ['url'] = addons_url ( 'Teacher://teacher/lists' );
        $res ['class'] = '';
        $nav [] = $res;

         $action = strtolower ( _ACTION );
        $res ['title'] = '微信信息';
        $res ['url'] = addons_url ( 'Teacher://weixin/lists' );
        $res ['class'] = 'current';
        $nav [] = $res;


        $this->assign ( 'nav', $nav );
    }

    function lists(){
        // data
        $list_data = $this->_get_model_list ($this->model);
        $opera_ref = 'binding&teacher_id='.$_REQUEST['teacher_id'].'&openid=[openid]|绑定';
        $list_data['list_grids'][sizeof($list_data['list_grids'])-1]['href'] = $opera_ref;

        //$list_data[]
        $this->assign ( $list_data );

        //设置显示控件
        $this->assign('check_all','0');
        $this->assign('add_button','0');
        $this->assign('del_button','0');

        // display
        $this->display ( 'lists' );
    }

    // bing the url
    function binding(){
        $url = addons_url('Teacher://teacher/lists');

        // teahcer
        $teacher_data =  M('teacher')->where('token="'.get_token().'" and  openid="'.$_REQUEST['openid'].'"'.'" and id<>"'.$_REQUEST['teacher_id'].'"')->find();
        if(!empty($teacher_data)){
            $this->error('已有教练['.$teacher_data['name'].']绑定此帐号!');
        }

        // studnt
        $student_data =  M('student')->where('token="'.get_token().'" and openid="'.$_REQUEST['openid'].'"')->find();
        if(!empty($student_data)){
            $this->error('已有学员['.$student_data['name'].']绑定此帐号!');
        }

        // save data
        $Model = M('teacher');
        $data['openid'] = $_REQUEST['openid'];
        $Model->where('id='.$_REQUEST['teacher_id'])->save($data);
        redirect($url);
    }
}