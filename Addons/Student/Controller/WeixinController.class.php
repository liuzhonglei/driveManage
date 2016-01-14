<?php

namespace Addons\Student\Controller;

use Think\Model;

class WeixinController extends StudentBaseController{
	function _initialize() {
        $this->model = $this->getModel ( 'follow' );
        parent::_initialize ();

        // 子导航
        $action = strtolower ( _ACTION );
        $res ['title'] = '信息';
        $res ['url'] = addons_url ( 'Student://student/lists' );
        $res ['class'] = '';
        $nav [] = $res;

        $res ['title'] = '未付款预约';
        $res ['url'] = addons_url('Student://student/signlist');
        $res ['class'] = ($action == 'signlist') ? 'cur' : '';
        $nav [] = $res;

        $res ['title'] = '已付款预约';
        $res ['url'] = addons_url('Student://student/payedSignList');
        $res ['class'] = ($action == 'payedsignlist') ? 'cur' : '';
        $nav [] = $res;

        $res ['title'] = '微信信息';
        $res ['url'] = addons_url ( 'Student://weixin/lists' );
        $res ['class'] = 'cur';
        $nav [] = $res;


        $this->assign ( 'sub_nav', $nav );
    }

    /**
     * 新界面显示的界面
     */
    protected function  setAdminModel(){
        // 调用父类
        parent::setAdminModel();

        // 设置操作栏
        $list_grids = $this->model['list_grid'];
        $position = strpos($list_grids,"ids");
        if($position > -1){
            $list_grids = substr($list_grids,0,$position);
        }
        $list_grids.= "\r\nopenid:15%操作:javascript_followBind('[openid]')|绑定";
        $this->model['list_grid'] = $list_grids;
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
        $this->assign('student_id',$_REQUEST['student_id']);

        // display
        $this->display ( 'lists' );
    }

    function binding(){
        $url = addons_url('Student://student/lists');

        // teacher
        $teacher_data =  M('teacher')->where('token="'.get_token().'" and openid="'.$_REQUEST['openid'].'"')->find();
        if(!empty($teacher_data)){
            $this->error('已有教练['.$teacher_data['name'].']绑定此帐号!');
        }

        // student
        $student_data =  M('student')->where('token="'.get_token().'" and openid="'.$_REQUEST['openid'].'" and id<>"'.$_REQUEST['student_id'].'"')->find();
        if(!empty($student_data)){
            $this->error('已有学员['.$student_data['name'].']绑定此帐号!');
        }

        // change
        $Model = M('student');
        $data['openid'] = $_REQUEST['openid'];
        $Model->where('id='.$_REQUEST['student_id'])->save($data);
        if($this->isAdmin()){
            $this->success();
        }else{
            redirect($url);
        }
    }
}