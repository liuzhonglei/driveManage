<?php

namespace Addons\Student\Controller;

use Think\Model;

class WeixinController extends StudentBaseController{
	function _initialize() {
        $this->model = $this->getModel ( 'follow' );
        parent::_initialize ();

        // 子导航
        $res ['title'] = '报名';
        $res ['url'] = addons_url('Student://student/signList');
        $res ['class'] =  '';
        $nav [] = $res;

        $action = strtolower ( _ACTION );
        $res ['title'] = '信息';
        $res ['url'] = addons_url ( 'Student://student/lists' );
        $res ['class'] = '';
        $nav [] = $res;

         $action = strtolower ( _ACTION );
        $res ['title'] = '微信信息';
        $res ['url'] = addons_url ( 'Student://weixin/lists' );
        $res ['class'] = 'cur';
        $nav [] = $res;


        $this->assign ( 'sub_nav', $nav );
    }

    function lists(){
        // data
        $list_data = $this->_get_model_list ($this->model);
        $opera_ref = 'binding&student_id='.$_REQUEST['student_id'].'&openid=[openid]|绑定';
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

    function binding(){
        $url = addons_url('Student://student/lists');

        // teahcer
        $teacher_data =  M('teacher')->where('token="'.get_token().'" and openid="'.$_REQUEST['openid'].'"')->find();
        if(!empty($teacher_data)){
            $this->error('已有教练['.$teacher_data['name'].']绑定此帐号!');
        }

        // studnt
        $student_data =  M('student')->where('token="'.get_token().'" and openid="'.$_REQUEST['openid'].'" and id<>"'.$_REQUEST['student_id'].'"')->find();
        if(!empty($student_data)){
            $this->error('已有学员['.$student_data['name'].']绑定此帐号!');
        }

        // change
        $Model = M('student');
        $data['openid'] = $_REQUEST['openid'];
        $Model->where('id='.$_REQUEST['student_id'])->save($data);
        redirect($url);
    }
}