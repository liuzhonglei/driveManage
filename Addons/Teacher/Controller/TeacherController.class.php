<?php

namespace Addons\Teacher\Controller;
use Addons\School\Controller\BaseController;

/**
 * Class TeacherController the teacher controller
 * @package Addons\Teacher\Controllere
 */
class TeacherController extends BaseController{
    /**
     * init
     */
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

    /**
     *  通用插件的列表模型
     * @param null $id_teacher
     */
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

    /**
     * binding the weixin code
     */
    function binding(){
        $param = array('teacher_id' => $_REQUEST['teacher_id']);
        $url = addons_url('Teacher://weixin/lists', $param);
        redirect($url);
    }
    
    /**
     * unbing the weixin code
     */
    function unbinding(){
        // change the data
        $Model = M('teacher');
        $data['openid'] = null;
        $Model->where('id='.$_REQUEST['teacher_id'])->save($data);

        // show
        $url = addons_url('Teacher://teacher/lists');
        redirect($url);
    }

    /**
     * edit the photos
     */
    function photos()
    {
        $param = array('type' => '1', 'object_id' => $_REQUEST['object_id'],'model' => 'teacher' );
        $url = addons_url('Teacher://teacherPhoto/lists', $param);
        redirect($url);
    }

    /**
     * show the teacher rank (front)
     */
    function show() {
        // data
        $list = M('teacher')->query('select @x:=ifnull(@x,0)+1 as rank, t.* from wp_teacher_rank_all t where token="'.get_token().'"');
        $this->assign('list',$list);

        // show
        $this->display ( T ( 'Addons://Teacher@Teacher/top' ) );
    }

    /**
     * get the school teacher data
     */
    function getTeachers(){
        $list = M ( 'teacher' )->query('select id, name text from wp_teacher t where t.token="'.get_token().'" and status="1"' );
        $this->ajaxReturn($list);
    }

    /**
     * get my teacher data
     */
    function getMyStudent(){
        $openid = get_openid();

        // find teacher
        $sql = <<<str
SELECT DISTINCT
	t.*, FROM_UNIXTIME(t.time_sign, "%Y-%m-%d") sign_date,t2.headimgurl,t3.name course_name
FROM
	wp_student t
INNER JOIN wp_teacher t1 ON (t.id_teacher_k2 = t1.id  and t.status ="2")
OR (t.id_teacher_k3 = t1.id and t.status ="3")
OR (t.id_in_teacher = t1.id and t.status="-1")
left join wp_follow t2 on t.openid = t2.openid
left join wp_school_course t3 on t.course_id = t3.id
str;
        $sql = $sql . " where t1.openid=\"" . $openid . "\" and t.status in (\"-1\",\"0\",\"1\",\"2\",\"3\",\"4\")";
        $data = M('student')->query($sql);
        $this->ajaxReturn($data, 'JSON');
    }
}
