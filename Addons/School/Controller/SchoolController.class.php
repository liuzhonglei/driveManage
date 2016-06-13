<?php
namespace Addons\School\Controller;

/**
 * the main school controller
 */

class SchoolController extends SchoolBaseController
{
    function _initialize()
    {
        $this->model = $this->getModel('school');
        $this->dataMultiEdit = true;

        parent::_initialize();
        // 子导航
        $action = strtolower(_ACTION);
        $res ['title'] = '信息';
        $res ['url'] = addons_url('School://school/lists');
        $res ['class'] = 'cur';
        $nav [] = $res;

        $this->assign('sub_nav', $nav);
    }

    public function index()
    {
        if (!is_login()) {
            Cookie('__forward__', $_SERVER ['REQUEST_URI']);
            $url = "Admin/index.html";
        } else {
            $url = "Admin/login.html";
        }
        redirect($url);
    }

    // 通用插件的列表模型
    public function lists()
    {
        // data
        $select_data = parent::_get_model_list($this->model);
        $this->assign($select_data);

        // plugin
        $this->assign('del_button', '0');
        $this->assign('search_button', '0');
        $this->assign('check_all', '0');
        if (sizeof($select_data['list_data']) > 0) {
            $this->assign('add_button', '0');
        }

        // display
        $this->display("lists");
    }

    /**
     * edit the page
     * @return [type] [description]
     */
    function photos()
    {
        $param = array('type' => '0', 'object_id' => $_REQUEST['object_id']);
        $url = addons_url('School://photo/lists', $param);
        redirect($url);
    }

    /**
     * show the page
     */
    function showPage()
    {
        $page = i('page');
        $this->display(T(MOBILE_PATH . $page));
    }

    /**
     * 显示轻轻宣传报名界面
     */
    function showRegister()
    {

        if ($_REQUEST['token']) {
            get_token($_REQUEST['token']);
        }
        get_openid();
        $this->display("mobile/page/register.html");


    }


    /**
     * show the index page
     * @return index page
     *
     */
    function show()
    {

        // gete compnay info
        if ($_REQUEST['token']) {
            $token = get_token($_REQUEST['token']);
        }
        get_openid($_REQUEST['openid']);
        $info = $this->getSchoolInfo();
        $this->assign($info);

        // get img
        $select_data = M('school_photo')->query('select t1.*, t2.path from wp_school_photo t1 left join wp_picture t2 on t1.photo = t2.id where t1.type="0" and t1.object_id = ' . $info['id'] . ' and  t1.token="' . $token . '" order by t1.sort');
        $this->assign('imgs', $select_data);

        // 判断
        $showUlr = "scene/" . get_token() . "/3dshow-mobile/index.html";
        if (!file_exists($showUlr)) {
            $showUlr = "#";
        }
        $this->assign('showUlr', $showUlr);

        // display
        $theme = $_REQUEST['theme'];
        $theme || $theme = "";
        $url = MOBILE_PATH . 'index' . $theme;
        $this->display(T($url));
    }

    /**
     * show the school plage navigator page
     * @return the page
     */
    function schoolNavigation()
    {
        $token = get_token();

        // 取得数据
        $_POST['token'] = get_token();
        $select_data = M('school_place')->query('select t1.*, t2.path from wp_school_place t1 left join wp_picture t2 on t1.photo = t2.id where t1.token="' . $token . '"');
        foreach ($select_data as &$data) {
            if (empty($data['coordinate'])) {
                continue;
            }

            $coor = explode(',', $data['coordinate']);
            $data['map_url'] = U('map', 'hor=' . $coor[0] . '&ver=' . $coor[1]);
        }


        $this->assign('list', $select_data);
        $this->display(T(MOBILE_PATH . 'schoolNavigation'));
    }

    /**
     * school register
     * @return the page
     */
    function schoolRegister()
    {
        if (IS_POST) {
            $_POST['token'] = get_token();
            $_POST['openid'] = get_openid();
            $_POST['status'] = '-1';
            $_POST['intro_source'] = '0';
            $_POST['time_sign'] = date("Y-m-d");
            if (empty($_POST['id_in_teacher'])) {
                $_POST['intro_source'] = '0';
            } else {
                $_POST['intro_source'] = '1';
            }
            parent::common_add($this->getModel('student'));
        } else {
            // 报名须知
            $sign_info = M('school_info')->where('token = "' . get_token() . '" and type = 0')->find();
            $lean_flow = M('school_info')->where('token = "' . get_token() . '" and type = 1')->find();
            $this->assign('sign_info', $sign_info['info']);
            $this->assign('learn_flow', $lean_flow['info']);

            // get data
            $data = M('school')->where('token = "' . get_token() . '"')->find();
            $this->assign('data', $data);
            $this->assign('section', $_REQUEST['section']);
            $this->display(T(MOBILE_PATH . 'schoolRegister'));
        }
    }

    /**
     * [ask description]
     * @return [type] [description]
     */
    function schoolQuestion()
    {
        $token = get_token();
        $_POST['token'] = get_token();
        $number = i('load_number');
        $sql = 'select t.*, from_unixtime(t.time) str_time,t1.nickname, t1.headimgurl, t2.path   from wp_student_question t left join wp_follow t1 on t.openid = t1.openid left join wp_picture t2 on t1.headimgurl = t2.id where t.token ="' . $token . '"  order by id desc ';
        $select_data = M('student_question')->query($sql);
        $this->assign('list', $select_data);
        // 取得数据
        $this->display(T(MOBILE_PATH . 'schoolQuestion'));
    }


    /**
     * show the position page
     * @return [type] [description]
     */
    function map()
    {
        $token = get_token();
        $this->assign('hor', $_REQUEST['hor']);
        $this->assign('ver', $_REQUEST['ver']);


        // 取得数据
        $this->display(T(MOBILE_PATH . 'schoolTeacherMap'));
    }


    /**
     * get the apprise data
     * @return [type] [description]
     */
    function getAppriseList()
    {
        $token = get_token();
        $_POST['token'] = get_token();
        $number = i('load_number');

        $db_config = D('Common/AddonConfig')->get("Message");
        $showAnswer = $db_config['show_no_answer_question'];
        $sql = 'select t.*, from_unixtime(t.time) str_time,t1.nickname, t1.headimgurl, t2.path   from wp_student_question t left join wp_follow t1 on t.openid = t1.openid left join wp_picture t2 on t1.headimgurl = t2.id where t.token ="' . $token . '" ';
        if ($showAnswer == "0") {
            $sql .= "and answer is not null ";
        }
        $sql .= 'order by id desc limit ' . $number;
        $select_data = M('student_question')->query($sql);

        $this->ajaxReturn($select_data);
    }


    /**
     * show the my center page
     */
    function myCenter()
    {
        if (IS_POST) {
            if (get_openid() == -1) {
                $this->error('抓取不到微信信息!');
            }

            $name = $_REQUEST['name'];
            $card_id = $_REQUEST['card_id'];
            $map = array('name' => $name, 'card_id' => $card_id);
            $myinfo = M('student')->where($map)->find();
            if (!empty($myinfo)) {
                $myinfo['openid'] = get_openid();
                M('student')->save($myinfo);
                $this->success();
            }
            $myinfo = M('teacher')->where($map)->find();
            if (!empty($myinfo)) {
                $myinfo['openid'] = get_openid();
                M('teacher')->save($myinfo);
                $this->success();
            } else {
                $this->error('查找不到对应注册人员!');
            }
        } else {

            $sql = 'select t.*, t2.headimgurl headimgurl from wp_student t left join wp_follow  t2 on t.openid = t2.openid where t.openid= "' . get_openid() . '" and t.token = "' . get_token() . '"';
            $myinfo = M('student')->query($sql)[0];
            if (!empty($myinfo)) {
                $this->assign('myinfo', $myinfo);
                $this->assign('schoolinfo', $this->getSchoolInfo());
                $this->display(T(MOBILE_PATH . 'studentCenter'));
            }
            $sql = 'select t.*, t2.headimgurl headimgurl from wp_teacher t left join wp_follow  t2 on t.openid = t2.openid where t.openid= "' . get_openid() . '" and t.token = "' . get_token() . '"';
            $myinfo = M('teacher')->query($sql)[0];
            if (!empty($myinfo)) {
                $this->assign('myinfo', $myinfo);
                //$this->assign('photo_path', get_cover_url($myinfo['photo']));
                $this->display(T(MOBILE_PATH . 'teacherCenter'));
            }
            if (empty($myinfo)) {
                $this->display(T(MOBILE_PATH . 'schoolCenterBind'));
            }
        }
    }


    /**
     * show the teahcer map navigator
     */
    function teacher_navigator()
    {
        // show
        $this->display(T(MOBILE_PATH . 'master_navigator'));
    }


    /**
     * show the my student page
     */
    function showMyStudents()
    {
        $this->display(T(MOBILE_PATH . 'teacherCenterStudentList'));
    }


    /**
     * return the recommandPage
     */
    function getRecommandPage()
    {
        // param
        $token = get_token();
        $openid = get_openid();
        $teacherInfo = M('teacher')->where("token = \"" . $token . "\" and openid = \"" . $openid . "\"")->find();
        if (!empty($teacherInfo)) {
            redirect(addons_url("Teacher://Teacher/teacherPage", array("teacher_id" => $teacherInfo["id"])));
        } else {
            redirect(addons_url("Student://Student/studentPayAdvance", array("token" => $token)));
        }
    }


    /**
     * 显示所有的驾校
     */
    function listAllSchool()
    {
        $data = M('school')->where(array('is_qingqing' => '1'))->select();
        $this->ajaxReturn($data);
    }

//
//    /**
//     * 取得驾校的信息
//     */
//    function getSchoolInfoByToken()
//    {
//        //  查找驾校信息
//        $token = $_REQUEST['token'];
//        $schoolInfo = M('school')->where(array('token' => $token))->find();
//
//        // 查找封面信息
//        $schoolInfo['photo'] = get_cover($schoolInfo['photo'])['path'];
//
//        // 返回
//        $this->ajaxReturn($schoolInfo);
//    }

    // function top()
    // {
    //     $vote_id = I('id', 0, 'intval');
    //     $openid = get_openid();
    //     $token = get_token();

    //     // 取得数据
    //     $list = M('teacher')->query('select @x:=ifnull(@x,0)+1 as rank, t.* from wp_teacher_rank_all t');

    //     //
    //     $this->assign('list', $list);
    //     $this->display(T(MOBILE_PATH.'top'));
    // }
}
