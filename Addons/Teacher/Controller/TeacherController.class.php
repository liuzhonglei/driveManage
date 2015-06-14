<?php

namespace Addons\Teacher\Controller;
use Addons\School\Controller\BaseController;


class TeacherController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'teacher' );
        parent::_initialize();

        // 子导航
        $action = strtolower ( _ACTION );
        $res ['title'] = '信息';
        $res ['url'] = addons_url ( 'Teacher://teacher/lists' );
        $res ['class'] = 'current';
        $nav [] = $res;

        $this->assign ( 'nav', $nav );
    }

    // 通用插件的列表模型
    public function lists($id_teacher = null)
    {
        // tips
        $normal_tips = '一般情况下请不要删除数据，会影响对应的学员与评价等相关数据。';
        $this->assign ( 'normal_tips', $normal_tips );

        // get data
        $list_data = $this->_get_model_list($this->model);

        // configure data
        $this->assign($list_data);

        // display
        $this->display("lists");
    }


    // show the teacher rank (front)
    function show() {
        // data
        $list = M('teacher')->query('select @x:=ifnull(@x,0)+1 as rank, t.* from wp_teacher_rank_all t where token="'.get_token().'"');
        $this->assign('list',$list);

        // show
        $this->display ( T ( 'Addons://Teacher@Teacher/top' ) );
    }


    // binding the weixin code
    function binding(){
        $param = array('teacher_id' => $_REQUEST['teacher_id']);
        $url = addons_url('Teacher://weixin/lists', $param);
        redirect($url);
    }

    // unbing the weixin code
    function unbinding(){
        // change the data
        $Model = M('teacher');
        $data['openid'] = null;
        $Model->where('id='.$_REQUEST['teacher_id'])->save($data);

        // show
        $url = addons_url('Teacher://teacher/lists');
        redirect($url);
    }


    //  lantern-slide
    function photos()
    {
        $param = array('type' => '1', 'object_id' => $_REQUEST['object_id'],'model' => 'teacher' );
        $url = addons_url('Teacher://teacherPhoto/lists', $param);
        redirect($url);
    }

}
