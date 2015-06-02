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


    //
//    public function score() {
//        // condition
//        $_POST['token'] = get_token ();
//
//        // 取得数据模型
//        $list_data = $this->_get_model_list ( $this->getModel('student_apprise') );
//
//        // teacher data
//        $list_data =  $this->replaceTeacerId($list_data,'id_teacher');
//        $list_data =  $this->replaceStudentId($list_data,'id_student');
//
//        // configure data
//        $this->assign ( $list_data );
//        // 显示
//        $this->display ( "lists" );
//    }

//    public function stu() {
//        // condition
//        $_POST['token'] = get_token ();
//
//        // 取得数据模型
//        $list_data = $this->_get_model_list ( $this->getModel('student') );
//
//        // teacher data
//        $list_data =  $this->replaceTeacerId($list_data,'id_teacher');
//        $list_data =  $this->replaceStudentId($list_data,'id_student');
//
//        // configure data
//        $this->assign ( $list_data );
//        // 显示
//        $this->display ( "lists" );
//    }


    //
//    public function replaceWeixin($list_data, $field){
//        // teacher data
//        $list = M ( 'follow' )->field ( 'openid,nickname' )->select ();
//        foreach ( $list as $vo ) {
//            $tea [$vo ['openid']] = $vo ['nickname'];
//        }
//        foreach ($list_data ['list_data'] as &$vo ) {
//            $vo [$field] = $tea [$vo [$field]];
//        }
//
//        return $list_data;
//    }

//
//
//    // 通用插件的增加模型
//    public function add() {
//        $_POST['token'] = get_token();
//        parent::common_add ( $this->model );
//    }
//
//    // 通用插件的编辑模型
//    public function edit() {
//        $_POST['token'] = get_token();
//        parent::common_edit ( $this->model );
//    }
//
//    // 通用插件的删除模型
//    public function del() {
//        $_POST['token'] = get_token();
//        parent::common_del ( $this->model );
//    }
//
//    // 替换教师id为姓名
//    public function replaceTeacerId($list_data, $field){
//        // teacher data
//        $list = M ( 'teacher' )->field ( 'id,name' )->select ();
//        foreach ( $list as $vo ) {
//            $tea [$vo ['id']] = $vo ['name'];
//        }
//        foreach ($list_data ['list_data'] as &$vo ) {
//            $vo [$field] = intval ( $vo [$field] );
//            $vo [$field] = $tea [$vo [$field]];
//        }
//
//        return $list_data;
//    }
//
//    // 替换教师id为姓名
//    public function replaceStudentId($list_data, $field){
//        // teacher data
//        $list = M ( 'student' )->field ( 'id,name' )->select ();
//        foreach ( $list as $vo ) {
//            $tea [$vo ['id']] = $vo ['name'];
//        }
//        foreach ($list_data ['list_data'] as &$vo ) {
//            $vo [$field] = intval ( $vo [$field] );
//            $vo [$field] = $tea [$vo [$field]];
//        }
//
//        return $list_data;
//    }
//

}
