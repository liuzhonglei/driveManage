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
        $res ['class'] = 'cur';
        $nav [] = $res;


        $this->assign ( 'sub_nav', $nav );
        $this->assign ( 'nav', null );
    }

    public function score() {
        // condition
        $_POST['token'] = get_token ();

        // 取得数据模型
        $list_data = $this->_get_model_list ( $this->getModel('student_apprise') );

        // condition
        $_POST['token'] = get_token ();

        // teacher data
        $list_data =  $this->replaceTeacerId($list_data,'id_teacher');
        $list_data =  $this->replaceStudentId($list_data,'id_student');

        // configure data
        $this->assign ( $list_data );
        // 显示
        $this->display ( "lists" );
    }

    public function stu() {
        // condition
        $_POST['token'] = get_token ();

        // 取得数据模型
        $list_data = $this->_get_model_list ( $this->getModel('student') );

        // teacher data
        $list_data =  $this->replaceTeacerId($list_data,'id_teacher');
        $list_data =  $this->replaceStudentId($list_data,'id_student');

        // configure data
        $this->assign ( $list_data );
        // 显示
        $this->display ( "lists" );
    }

    // 通用插件的列表模型
    public function lists($id_teacher = null)
    {


        // 使用提示
        $map ['token'] = get_token();
        session('common_condition', $map);

        // condition
        $_POST['token'] = get_token();

        // get data
        $list_data = $this->_get_model_list($this->model);

        // teacher data
        $list_data = $this->replaceWeixin($list_data, 'open_id');

        // configure data
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    public function replaceWeixin($list_data, $field){
        // teacher data
        $list = M ( 'follow' )->field ( 'openid,nickname' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['openid']] = $vo ['nickname'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }



    // 通用插件的增加模型
    public function add() {
        $_POST['token'] = get_token();
        parent::common_add ( $this->model );
    }

    // 通用插件的编辑模型
    public function edit() {
        $_POST['token'] = get_token();
        parent::common_edit ( $this->model );
    }

    // 通用插件的删除模型
    public function del() {
        $_POST['token'] = get_token();
        parent::common_del ( $this->model );
    }

    function show() {
        $vote_id = I ( 'id', 0, 'intval' );
        $openid = get_openid ();
        $token = get_token ();

        // 取得数据
        $list = M('teacher')->query('select @x:=ifnull(@x,0)+1 as rank, t.* from wp_teacher_rank_all t');


        //
        $this->assign('list',$list);
        $this->display ( T ( 'Addons://Teacher@Teacher/top' ) );
    }

    // 替换教师id为姓名
    public function replaceTeacerId($list_data, $field){
        // teacher data
        $list = M ( 'teacher' )->field ( 'id,name' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['id']] = $vo ['name'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = intval ( $vo [$field] );
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }

    // 替换教师id为姓名
    public function replaceStudentId($list_data, $field){
        // teacher data
        $list = M ( 'student' )->field ( 'id,name' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['id']] = $vo ['name'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = intval ( $vo [$field] );
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }


    // binding the weixin code
    function binding(){
        $param = array('teacher_id' => $_REQUEST['teacher_id']);
        $url = addons_url('Teacher://weixin/lists', $param);
        redirect($url);
    }
}
