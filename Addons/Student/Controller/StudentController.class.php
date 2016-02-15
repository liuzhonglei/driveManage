<?php

namespace Addons\Student\Controller;

use Addons\School\Controller\Common\Log\LogController;

use Addons\School\Controller\Common\Log\LogMajorType;
use Addons\School\Controller\Common\Msg\MessageController;

use Addons\EO2OPayment\Controller\EO2OPaymentController;

require_once ONETHINK_ADDON_PATH . '/Third/Controller/simple_html_dom.php';
//require_once __ROOT__ . '/Application/Common/Common/weixinFunction.php';
//require_once __ROOT__ . '/Application/Common/Common/syncFunction.php';


define('SCHOOL_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Public');

/**
 * student controller
 * Class StudentController
 * @package Addons\Student\Controller
 */
class StudentController extends StudentBaseController
{
    /**
     * init
     */
    function _initialize()
    {

        $this->model = $this->getModel('student');
        $this->dataMultiEdit = true;
        parent::_initialize();
        $action = strtolower(_ACTION);

        // 子导航
        $res ['title'] = '信息';
        $res ['url'] = addons_url('Student://student/lists');
        $res ['class'] = ($action == 'lists') ? 'cur' : '';
        $nav [] = $res;

        $res ['title'] = '未付款预约';
        $res ['url'] = addons_url('Student://student/signlist');
        $res ['class'] = ($action == 'signlist') ? 'cur' : '';
        $nav [] = $res;

        $res ['title'] = '已付款预约';
        $res ['url'] = addons_url('Student://student/payedSignList');
        $res ['class'] = ($action == 'payedsignlist') ? 'cur' : '';
        $nav [] = $res;

        // assign
        $this->assign('sub_nav', $nav);

    }


    /**
     * show the liests
     * @param null $id_teacher
     */
    public function  lists($id_teacher = null)
    {
        // tips
        $normal_tips = '一般情况下请不要删除数据，会影响对应的与评价等相关数据。';
        $this->assign('normal_tips', $normal_tips);
        $top_more_button = array(array("is_buttion" => "0", "title" => "EXL导出", "url" => U("listsExcel")));
        $this->assign('top_more_button', $top_more_button);
        $this->assign('searchAction', "lists");

        // get data
        $list_data = $this->_get_model_list($this->model);

        // configure data
        $this->assign('file_button', 1);
        $this->assign($list_data);

        // display
        $this->display("lists");
    }


    /**
     * 增加划款事项
     * @param $fields
     * @return \Addons\School\Controller\Common\Controller\当前的字段
     */
    public function getFieldList($fields)
    {
        $fields = parent::getFieldList($fields);
        $fields = $this->setFiledExtra($fields, "sign_place_id", 'school_place', 'name');

        return $fields;
    }

    /**
     * 查找当前字段配置
     */
    public function getFieldConf()
    {
        $list_data = $this->_list_grid($this->model);
        $conf = M('field_display_conf')->where(array("token" => get_token(), "status" => $_REQUEST["status"], "model" => "student"))->find()["value"];
        if (!empty($conf)) {
            $confFieldList = explode(",", $conf);
            for ($index = 0; $index < count($list_data ['list_grids']);) {
                if (!in_array($list_data ['list_grids'][$index]["field"][0], $confFieldList)) {
                    $list_data ['list_grids'][$index]["show"] = false;
                }
                $index++;
            }
        }

        // 返回
        $this->success("显示对应信息", null, null, $list_data ['list_grids']);
    }

    /**
     *
     * 保存当前字段显示配置
     * @return mixed
     */
    public function saveFieldConf()
    {
        $model = M('field_display_conf');
        $info = $model->where(array("token" => get_token(), "status" => intval($_REQUEST["status"]), "model" => "student"))->find();
        if ($info) {
            $info["value"] = i("value");
            $result = $model->save($info);
        } else {
            $result = $model->add(array("token" => get_token(), "status" => intval($_REQUEST["status"]), "model" => "student", "value" => i("value")));
        }
        return $this->success($result);
    }

    /**
     * 获取数据
     * @param null $model
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = 'id desc')
    {
        $page || $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $conf = M('field_display_conf')->where(array("token" => get_token(), "status" => $_REQUEST["status"], "model" => "student"))->find()["value"];
        if (!empty($conf)) {
            $confFieldList = explode(",", $conf);
            for ($index = 0; $index < count($list_data ['fields']);) {
                if (!in_array($list_data ['fields'][$index], $confFieldList)) {
                    array_splice($list_data ['fields'], $index, 1);
                    continue;
                }
                $index++;
            }
        }

        $conf = M('field_display_conf')->where(array("token" => get_token(), "status" => $_REQUEST["status"], "model" => "student"))->find()["value"];
        if (!empty($conf)) {
            $confFieldList = explode(",", $conf);

            for ($key = 0; $key < count($list_data["list_grids"]);) {
                $value = $list_data["list_grids"][$key]["field"][0];
                if (empty($value) || in_array(explode("|", $value)[0], $confFieldList)) {
                    $key++;
                    continue;
                } else {
                    array_splice($list_data["list_grids"], $key, 1);
                }
            }
        }

        // 设置查询字段
        $fields = $list_data ['fields'];

        // 搜索条件
        $fieldValue = "";
        foreach ($_REQUEST as $name => $value) {
            if (strpos($name, ',')) {
                $fieldValue = $value;
            }
        }

        // 查询条件
        $model_fields = M('attribute')->where('model_id=' . $model ['id'])->field('name')->select();
        $mapField = Array();
        foreach ($model_fields as $filed) {
            array_push($mapField, $filed['name']);
        }
        $map = $this->_search_map($model, $mapField);
        $map = $this->convertMap($map) . $this->getPayOpenid();;

        // 增加日期限制
        if (!empty($_REQUEST["sign_begin_date"])) {
            $map .= " and time_sign >= " . $_REQUEST["sign_begin_date"];
        }
        if (!empty($_REQUEST["sign_end_date"])) {
            $map .= " and time_sign <= " . $_REQUEST["sign_end_date"];
        }

        if (!empty($fieldValue)) {
            $ids = $this->getStudentOpenids($fieldValue, "id");
            $openids = $this->getFollowOpneids($fieldValue, "openid");
            if (count($ids) < 1 && count($openids) < 1) {
                $map .= " and 1 = 0";
            } else {
                $connectSql = "";
                if (count($ids) > 0) {
                    $connectSql .= "id in (" . $this->getArrayStr($ids) . ")";
                }
                if (count($openids) > 0) {
                    if (!empty($connectSql)) {
                        $connectSql .= " or ";
                    }
                    $connectSql .= "openid in (" . $this->getArrayStr($openids) . ")";
                }
                $map .= "and ( " . $connectSql . " )";
            }
        }


        // 关键字搜索
        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];
        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');

        // special handle
        if (!empty($this->listsTable)) {
            $name = $this->listsTable;
        } else {
            $name = get_table_name($model ['id']);
        }

        //$name = parse_name ($name, true );
        $sql = $this->createStudentSql($fields, $map, $page, $row, $order);
        $data = M($name)->query($sql);

        /* 查询记录总数 */
        $count = M('student')->execute("select * from wp_student  t  where " . $map);

        $list_data ['list_data'] = $data;
        $list_data ['count'] = $count;

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $list_data ['_page'] = $page->show();
        }

        //反悔
        return $list_data;
    }


    /**
     * 数组转换成 字符串条件
     */
    private function  convertMap($map)
    {
        $mapSql = "";
        if (is_array($map)) {
            foreach ($map as $key => $value) {
                if (!empty($mapSql)) {
                    $mapSql .= " and ";
                }
                if (in_array($key, array("phone", "name", "card_id"))) {
                    $mapSql .= $key . " like '%" . $value . "%' ";
                } else {
                    $mapSql .= $key . " = '" . $value . "'";
                }
            }
        } else {
            $mapSql = $map;
        }

        return $mapSql;
    }

    /**
     * create the student execute sql
     * @param $fields
     * @param $map
     * @param $page
     * @param $row
     * @param $order
     * @return string
     */
    private function createStudentSql($fields, $map, $page, $row, $order = "id desc")
    {
        $order || $order = "id desc";
        $fieldsSql = implode(",", $fields);
        $mapSql = "";
        if (is_array($map)) {
            foreach ($map as $key => $value) {
                if (!empty($mapSql)) {
                    $mapSql .= " and ";
                }
                $mapSql .= $key . " = '" . $value . "'";
            }
        } else {
            $mapSql = $map;
        }
        if (!empty($mapSql)) {
            $mapSql = "where " . $mapSql;
        }

        $limitSql = "";
        if (!empty($page) && !empty($row)) {
            $limitBegin = ($page - 1) * $row;
            $limitSql = "limit " . $limitBegin . "," . $row;
        }


        $sql = <<<STR
        select $fieldsSql from (
        select t.*,(
				SELECT
					NAME
				FROM
					wp_teacher
				WHERE
					token = t.token
				AND id = t.id_teacher_k2
				LIMIT 1
			) teacher_k2_name,
			t3. NAME course_name,
			t5. NAME status_name,
			(
				SELECT
					NAME
				FROM
					wp_teacher
				WHERE
					token = t.token
				AND id = t.id_teacher_k3
				LIMIT 1
			) teacher_k3_name,
			(
				CASE t.intro_source
				WHEN '1' THEN
					(
						SELECT
							NAME
						FROM
							wp_teacher
						WHERE
							token = t.token
						AND id = t.id_in_teacher
						LIMIT 1
					)
				WHEN '2' THEN
					(
						SELECT
							NAME
						FROM
							wp_student
						WHERE
							t.token = token
						AND t.in_student_openid = openid
						LIMIT 1
					)
				ELSE
					''
				END
			) in_name,
			(
				SELECT
					nickname
				FROM
					wp_follow
				WHERE
					token = t.token
				AND openid = t.openid
				LIMIT 1
			) weixin_name,
			t8.total_fee
		FROM
			(
			select t.* from
			wp_student t
			$mapSql
             order by $order
            $limitSql ) t
		LEFT JOIN wp_school_course t3 ON t.token = t3.token
		AND t.course_id = t3.id
		LEFT JOIN wp_school_dict t5 ON t5.dic_type = 'student_status'
		AND t. STATUS = t5.
		VALUE

		LEFT JOIN wp_eo2o_payment_count t8 ON t.token = t8.token
		AND t.openid = t8.openid
     ) tw
     order by $order

STR;

        // return
        return $sql;
    }

    /**
     * download the studentl lists
     */
    public function listsExcel()
    {
        $this->model ['list_row'] = 100000;
        // get data
        $list_data = $this->_get_model_list($this->model);
        $this->downloadExcel($list_data);
    }

    /**
     * get the sign student data
     * @param bool $payed the student is payed
     */
    private function getSignData($payed = false)
    {
        $_REQUEST['payed'] = $payed;
        return $this->_get_model_list($this->model);
    }

    /**
     * show the sign student list
     */
    public function signList()
    {
        // get data
        $top_more_button = array(array("is_buttion" => "0", "title" => "EXL导出", "url" => U("signListExcel")));
        $this->assign('top_more_button', $top_more_button);
        $this->assign('searchAction', "signList");

        // if referrer is student set in_name

        $list_data = $this->getSignData(false);

        // configure data
        $list_data['list_grids'][count($list_data['list_grids']) - 1]['href'] = 'signConfirm&student_id=[id]|确认,signCancel&student_id=[id]|取消,inPay&student_id=[id]|推荐费已支付';
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * show the  sign student list excel
     */
    public function signListExcel()
    {
        $this->model ['list_row'] = 100000;
        $this->downloadExcel($this->getSignData(false));
    }

    /**
     * show the sign student that have payed
     */
    public function payedSignList()
    {
        // get data
        $top_more_button = array(array("is_buttion" => "0", "title" => "EXL导出", "url" => U("payedSignListExcel")));
        $this->assign('top_more_button', $top_more_button);
        $this->assign('searchAction', "payedSignList");

        // if referrer is student set in_name
        $list_data = $this->getSignData(true);

        // configure data
        $list_data['list_grids'][count($list_data['list_grids']) - 1]['href'] = 'signConfirm&student_id=[id]|确认,signCancel&student_id=[id]|取消,inPay&student_id=[id]|推荐费已支付';
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * down the sign student excel that have payed
     */
    public function payedSignListExcel()
    {
        $this->model ['list_row'] = 100000;
        $this->downloadExcel($this->getSignData(true));
    }


    public function signconfirm()
    {
        $id = $_REQUEST['student_id'];
        M('student')->where('id=' . $id)->setField('status', '0');
        $url = addons_url('Student://student/signList');
        redirect($url);
    }

    public function signCancel()
    {
        $id = $_REQUEST['student_id'];
        M('student')->where('id=' . $id)->setField('status', '-2');
        $url = addons_url('Student://student/signList');
        redirect($url);
    }

    public function edit()
    {
        //检查是否修改状态，并记录日志
        if (IS_POST) {
            $id = $_REQUEST['id'];
            $student = M('student')->where('id = ' . $id)->find();
            if (!empty($student)) {
                $status = $_REQUEST['status'];
                if ($status != $student['status']) {
                    $logController = new   LogController();
                    $originalStatus = get_name_by_status($student['status'], 'status', $this->model['id']);
                    $currentStatus = get_name_by_status($status, 'status', $this->model['id']);
                    $logController->writeLog("学员状态发生变化，状态[" . $originalStatus . "]->状态[" . $currentStatus . "]", LogMajorType::STUDENT, $id);
                }
            }
        }

        //修改
        parent::edit();
    }


    /**
     * create the student conf map
     */
    private function getFieldMap()
    {
        $field_maps = array();
        $fields = M('attribute')->query('select t.* from wp_attribute t left join wp_model t1 on t.model_id = t1.id where t1.name ="student"  and t.extra != "";');
        foreach ($fields as $field) {
            // field_map
            $field_map = array();
            $maps = explode("\r\n", $field['extra']);
            foreach ($maps as $map) {
                $value_key = explode(":", $map);
                if (!empty($value_key[1])) {
                    $field_map[$value_key[1]] = $value_key[0];
                }
            }
            //add
            $field_maps[$field['name']] = $field_map;
        }

        return $field_maps;
    }

    /**
     * 查看学员的详情
     */
    function detail()
    {

        $param = array('student_id' => $_REQUEST['student_id']);
        $url = addons_url('Student://detail/log', $param);
        redirect($url);
    }

    // binding the weixin code
    function binding()
    {
        $param = array('student_id' => $_REQUEST['student_id']);
        $url = addons_url('Student://weixin/lists', $param);
        redirect($url);
    }

    // unbingding the weixin code
    function unbinding()
    {
        // change the data
        $Model = M('student');
        $data['openid'] = null;
        $Model->where('id=' . $_REQUEST['student_id'])->save($data);

        // show
        if ($this->isAdmin()) {
            $this->success();
        } else {
            $url = addons_url('Student://student/lists');
            redirect($url);
        }
    }

    /**
     * set the weixin student in student payed
     */
    function inPay()
    {
        $Model = M('student');
        $data['is_in_payed'] = "1";
        $Model->where('id=' . $_REQUEST['student_id'])->save($data);

        // show
        if ($this->isAdmin()) {
            $this->success();
        } else {


            $url = $_SERVER['HTTP_REFERER'];
            redirect($url);
        }
    }


    /**
     * get the model value
     * @param $modelName
     * @param $map
     * @return string id:value
     */
    function getData($modelName, $map)
    {
        $extra = ':' . "\r\n";
        $list = M($modelName)->where($map)->select();

        foreach ($list as $v) {
            $extra .= $v ['id'] . ':' . $v ['name'] . "\r\n";
        }

        // return
        return $extra;
    }

    /**
     * show my teacher page
     */
    function myTeacher()
    {
        // param
        $openid = get_openid();
        $token = get_token();

        // get the student data
        $student = M('student')->where('openid= "' . $openid . '" and token = "' . $token . '"')->find();
        $teacherList = array();
        if (!empty($student)) {
            $ids = '';
            !$student['id_teacher_k2'] || $ids .= '"' . $student['id_teacher_k2'] . '"';
            !$student['id_teacher_k3'] || $ids .= ',"' . $student['id_teacher_k3'] . '"';
            $sql = 'select t.*, t1.path headimgurl from wp_teacher t  LEFT JOIN wp_picture t1 ON t.photo = t1.id where t.token = "' . $token . '" and t.id in (' . $ids . ')';
            $teacherList = M('teacher')->query($sql);
        }
        if ($student['id_teacher_k2'] == $student['id_teacher_k3'] && $teacherList != false && sizeof($teacherList) > 0) {
            $teacherList[0]['bindname'] = "科目二教练 科目三教练";
        } else {
            if ($teacherList[0] != null) {
                if ($teacherList[0]['id'] == $student['id_teacher_k2']) {
                    $teacherList[0]['bindname'] = "科目二教练";
                } else {
                    $teacherList[0]['bindname'] = "科目三教练";
                }


            }
            if ($teacherList[1] != null) {
                if ($teacherList[1]['id'] == $student['id_teacher_k2']) {
                    $teacherList[1]['bindname'] = "科目二教练";
                } else {
                    $teacherList[1]['bindname'] = "科目三教练";
                }
            }
        }

        $this->assign('teacherList', $teacherList);
        $this->assign('student', $student);


        // show
        $this->display(T(MOBILE_PATH . 'studentCenterTeacherList'));
    }


    /**
     * show comment teacher page
     */
    function commentTeacher()
    {
        // param
        $token = get_token();
        $Model = M('student_apprise');

        $teacher_id = i('teacher_id', 0, 'intval');
        $student_id = i('student_id', 0, 'intval');

        if (IS_POST) {
            $level = i('value', 0, 'intval');
            $apprise = i('comment');

            $time = time();

            $data = $Model->where(array('id_teacher' => $teacher_id, 'id_student' => $student_id, 'token' => $token))->find();
            $result = true;
            if (!empty($data) && $data['level'] == $level && $data['apprise'] == $apprise) {
                $result = true;
            } else if ($data) {
                $data['level'] = $level;
                $data['apprise'] = $apprise;
                $result = $Model->save($data);
            } else {
                $data = array('id_teacher' => $teacher_id, 'id_student' => $student_id, 'level' => $level, 'token' => $token, 'apprise' => $apprise, 'time' => $time);
                $result = $Model->add($data);
            }

            if ($result) {
                $return = array('status' => '0');
            } else {
                $return = array('status' => '-1', 'error' => $Model->getDbError());
            }
            $this->ajaxReturn($return);
        } else {
            $this->assign($_GET);

            $sql = 'select t.*, t1.path headimgurl from wp_teacher t  LEFT JOIN wp_picture t1 ON t.photo = t1.id where t.token = "' . $token . '" and t.id = "' . $teacher_id . '"';
            $teacher = M('teacher')->query($sql)[0];
            if ($teacher != null && empty($teacher['level'])) {
                $teacher['level'] = 0;
            }
            $this->assign('teacher', $teacher);

            $data = $Model->where(array('id_teacher' => $teacher_id, 'id_student' => $student_id, 'token' => $token))->find();
            if (empty($data)) {
                $data = array("level" => 4);
            }
            $this->assign('data', $data);

            // show
            $this->display(T(MOBILE_PATH . 'studentCenterCommentTeacher'));
        }
    }

    /**
     * show the binding search teacher page
     */
    function searachBindTeacher()
    {
        // param

        $token = get_token();

        if (IS_POST) {
            $name = i('name');
            $sql = 'select t.*, t1.path headimgurl from wp_teacher t  LEFT JOIN wp_picture t1 ON t.photo = t1.id where t.token = "' . $token . '" and t.name like "%' . $name . '%"';


            $teacherList = M('teacher')->query($sql);
            $this->ajaxReturn($teacherList);
        } else {
            $this->display(T(MOBILE_PATH . 'studentCenterBindTeacher'));
        }
    }

    /**
     * student binding the teacher
     */
    function bindTeacher()
    {
        // param
        $teacherId = i('teacherId');
        $openid = get_openid();
        $token = get_token();
        $Model = M('student');
        $course = i('course');

        // get student
        $student = M('student')->where('openid="' . $openid . '" and token = "' . $token . '"')->find();

        // check
        $student || $this->ajaxReturn(array('status' => '-1', 'error' => '微信没有绑定学员！'));
        if (!empty($student['id_teacher_k' . $course])) {
            $this->ajaxReturn(array('status' => '-1', 'error' => '学员已经绑定相应`教练，需要修改请于驾校联系！'));
        }

        // modify and save
        $student['id_teacher_k' . $course] = $teacherId;
        $Model->save($student);

        // return
        $this->ajaxReturn(array('status' => '0'));

    }

    /**
     * pay the money
     */
    function studentCenterBannerPay()
    {
        $sql = 'select t.*, t1.path headimgurl from wp_teacher t  LEFT JOIN wp_picture t1 ON t.photo = t1.id where  t.id = ' . i('teacher_id');
        $teacherInfo = M('teacher')->query($sql)[0];
        $this->assign("teacherInfo", $teacherInfo);
        $this->assign($_REQUEST);
        $this->display(T(MOBILE_PATH . 'studentCenterBannerPay'));
    }

    /**
     * the pay list
     */
    function studentCenterPayList()
    {
        $Model = M("eo2o_payment_all");
        $token = get_token();
        $openid = get_openid();
        $payList = $Model->where(" LENGTH(trim(transaction_id))>0 and token=\"" . $token . "\" and openid=\"" . $openid . "\"")->order("id desc")->select();
        $this->assign("payList", $payList);
        $this->display(T(MOBILE_PATH . 'studentCenterPayList'));
    }

    /**
     * pay the money
     */
    function studentCenterPay()
    {
        $token = get_token();
        parent::checkWeixinBind();

        $schoolInfo = $this->getSchoolInfo();
        $this->assign("schoolInfo", $schoolInfo);
        $payitemList = M("school_payitem")->where("token=\"" . $token . "\"")->order('id desc')->select();
        $this->assign("payitemList", $payitemList);
        $this->display(T(MOBILE_PATH . 'studentCenterPay'));
    }

    /**
     * get the student info
     * @return student info
     */
    public function getMyInfo()
    {
        $token = get_token();
        $openid = get_openid();
        $student = M('student')->where('openid= "' . $openid . '" and token = "' . $token . '"')->find();
        return $student;
    }

    /**
     * somebody want to pay from me
     * @return the page student pay advance
     */
    function studentPayAdvance()
    {
        // param
        $id_in_teacher = $_GET["id_in_teacher"];
        if (!empty($id_in_teacher)) {
            $teacherInfo = M('teacher')->where('id=' . $id_in_teacher)->find();
            get_token($teacherInfo['token']);
        }
        $openid = get_openid();
        $token = get_token();
        $in_student_openid = $_GET["in_student_openid"];
        $studentInfo = $this->getMyInfo();

        // judge get or post
        if (!IS_POST) {
            // set the page param
            $schoolInfo = $this->getSchoolInfo();
            $schoolInfoUrl = get_cover_url($schoolInfo["photo"]);
            $this->assign("schoolInfo", $schoolInfo);
            $this->assign("photoUrl", $schoolInfoUrl);
            $follow = M('follow')->where('openid= "' . get_openid() . '" and token = "' . $token . '"')->find();
            $this->assign("follow", $follow);
            if (!empty($in_student_openid)) {
                $inStudentInfo = M('student')->where('openid= "' . $in_student_openid . '" and token = "' . $token . '"')->find();
                $inFollow = M('follow')->where('openid= "' . $in_student_openid . '" and token = "' . $token . '"')->find();

                if (!empty($inFollow)) {
                    $this->assign("photoUrl", $inFollow["headimgurl"]);
                }
                $this->assign("inStudentInfo", $inStudentInfo);
            } else {
                $this->assign("inStudentInfo", $schoolInfo);
            }

            $this->assign("studentInfo", $studentInfo);
            if (!empty($studentInfo) && ((!empty($studentInfo['total_fee']) && $studentInfo['status'] == "-1") || $studentInfo['status'] != "-1")) {
                $this->assign("signHide", "hidden");
                $this->assign("signText", "您已经在本驾校登记过了，请耐心等待通知！");
            } else {
                $this->assign("signHide", "");
                $this->assign("signText", "同时为我赢得100元学车基金哦！");
            }

            $this->display(T(MOBILE_PATH . 'studentPayAdvance'));
        } else {
            // check is teacher
            $existTeacherInfo = M("teacher")->where('openid= "' . $openid . '" and token = "' . $token . '"')->find();
            if (!empty($existTeacherInfo)) {
                $this->error("当前微信号已与教练[" . $existTeacherInfo['name'] . "]绑定。如果有误，请与驾校联系！");
            }

            if (!empty($studentInfo)) {
                $_POST['id'] = $studentInfo["id"];
            }
            // add the student
            $_POST['token'] = $token;
            $_POST['openid'] = $openid;
            $_POST['status'] = '-1';
            $_POST['intro_source'] = '0';
            $_POST['time_sign'] = date("Y-m-d");
            $_POST['name'] = $_GET['name'];
            $_POST['course_id'] = $_GET['course_id'];
            $_POST['phone'] = $_GET['phone'];
            $_POST['remark'] = $_GET['remark'];
            $_POST['id_in_teacher'] = $_GET['id_in_teacher'];
            $_POST['in_student_openid'] = $in_student_openid;
            if (!empty($id_in_teacher)) {
                $_POST['intro_source'] = '1';
            } else if (!empty($in_student_openid)) {
                $_POST['intro_source'] = '2';
            } else {
                $_POST['intro_source'] = '0';
            }
            $result = $this->saveModel();
            if ($result['status'] == '0') {
                $this->ajaxReturn($result);
            }

            // start the pay
            $payItme = M("school_payitem")->where("type='activity' and token = '" . $token . "'")->find();
            $_POST["payitem_id"] = $payItme["id"];
            $_POST["paytype"] = $payItme["type"];
            $_POST["total"] = $payItme["fee"];
            R('Addons://EO2OPayment/EO2OPayment/paymentData');
        }
    }


    /**
     * return the student pay advance result
     */
    function studentPayAdvanceResult()
    {
        $follow = M('follow')->where('openid= "' . get_openid() . '" and token = "' . get_token() . '"')->find();
        $this->assign("follow", $follow);
        $schoolInfo = $this->getSchoolInfo();
        $this->assign("schoolInfo", $schoolInfo);
        $studentInfo = $this->getMyInfo();
        $this->assign("studentInfo", $studentInfo);
        $db_config = D('Common/AddonConfig')->get("Leaflets");
        $imgUrl = get_cover_url($db_config['img']);
        $this->assign("imgUrl", $imgUrl);
        $this->display(T(MOBILE_PATH . 'studentPayAdvanceResult'));
    }


    /**
     * get my recommand student data
     */
    function getMyStudent()
    {
        // param
        $token = get_token();
        $openid = get_openid();
        $where = 'token = "' . $token . '" and openid = "' . $openid . '"';
        $queryParam = "";

        // current user is student or teacher
        $student = M("student")->where($where)->find();
        $teacher = M("teacher")->where($where)->find();
        if (!empty($student)) {
            $queryParam = 't.in_student_openid="' . $openid . '"';
        } else if (!empty($teacher)) {
            $queryParam = 't.id_in_teacher="' . $teacher['id'] . '"';
        } else {
            $this->ajaxReturn("", 'JSON');
        }


        // find student
        $sql = <<<str
SELECT DISTINCT
	t.*, FROM_UNIXTIME(t.time_sign, "%Y-%m-%d") sign_date,t2.headimgurl
FROM
	wp_student t
left join wp_follow t2 on t.token = t2.token and  t.openid = t2.openid
where $queryParam
order by t.id desc
str;
        $data = M('student')->query($sql);

        // return
        $this->ajaxReturn($data, 'JSON');
    }


    /**
     * 取得驾校的所有学员
     */
    public function  getStudents()
    {
        $list = M('student')->query('select id, name text from wp_student t where t.token="' . get_token() . '"');
        $this->ajaxReturn($list);
    }

    /**
     * 增加学员的划款流水
     */
    function  moneyLogAdd()
    {
        $where = 'id  = ' . $_REQUEST['student_id'] . '';
        $student = M("student")->where($where)->find();
        $_REQUEST['openid'] = $student['openid'];

        $paymentController = new EO2OPaymentController();

        if ($this->isAdmin()) {
            $paymentController->addAdmin();
        } else {
            $nav = $this->get('nav');
            $subNav = $this->get('sub_nav');
            $paymentController->add($nav, $subNav);
        }
    }

    /**
     * 同步所有驾校学员信息
     */
    function syncAllStudentInfo()
    {
        $data = M("member_public")->select();
        foreach ($data as $item) {
            if (!empty($item["token"])) {
                $this->syncStudent(null, $item["token"], true, false);
            }
        }

        $this->success("成功同步");
    }

    /**
     *  配置对应的账户 要系统配置当中
     * 同步学员信息
     * @param $status 学员状态
     * @param $token 驾校token
     * @param $isAll 是否全同步
     */
    function syncStudent($status = null, $token = null, $isAll = false, $ajaxReturn = true)
    {

        // 取得账户密码
        $token || $token = get_token();
        $db_config = D('Common/AddonConfig')->get("Student", $token);
        $account = $db_config["sync_account"];
        $password = $db_config["sync_password"];
        $taskSign = $db_config["sync_task"];


        //判读是否执行同步
        if (empty($account) || empty($password) || ($isAll && $taskSign == "0")) {
            return;
        }


        // 组装查询条件
        if ($isAll) {
            $map = array();
        } else {
            $status || $status = i("status");
            $statusMap = array("1" => "录入", "2" => "科目一通过", "3" => "科目二通过", "4" => "科目三通过", "99" => "结业");
            if ($status) {
                $status = $statusMap[$status];
                if (empty($status)) {
                    return;
                }
            }
            $today = date("Y-m-d");
            $map = array("applyStartTime" => $today, "applyEndTime" => $today, "state" => $status);
        }


        // 登录
        $cookiePath = "./Runtime/School/wudriver.cookie";
        $this->login_post("http://fj.jppt.com.cn/xmjp/loginSubmit.do", $cookiePath, array("loginName" => $account, "password" => $password, "loginFlag" => "pw"));

        // 查询
        $return = $this->get_post_content("http://fj.jppt.com.cn/xmjp/student/basic/main.do", $cookiePath, $map);
        $html = new \simple_html_dom($return);
        $pageInfo = $html->find('.pleft', 0)->plaintext;
        $pageNum = intval(substr($pageInfo, strpos($pageInfo, "共") + 3, strpos($pageInfo, "条") - strpos($pageInfo, "共") - 3));

        $everyPageNum = 300;
        $htmlDom = new \simple_html_dom();
        for ($pageIndex = 1, $totalNum = 0; $totalNum < $pageNum; $totalNum += $everyPageNum, $pageIndex++) {
            $return = $this->get_post_content("http://fj.jppt.com.cn/xmjp/student/basic/main.do?pageIndex=" . $pageIndex . "&pageSize=" . $everyPageNum, $cookiePath);

            // 转换
            $htmlDom->load($return);
            $htmlDataList = $htmlDom->find('#table-1', 0)->find('tr');
            array_shift($htmlDataList);
            foreach ($htmlDataList as $value) {
                $studentInfo = array();
                $index = 2;
                $studentInfo["name"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["sex"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["course_id"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["time_sign"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["card_id"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["phone"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["teacher_k1"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["teacher_k2"] = trim($value->children($index)->plaintext);
                $index++;

                $studentInfo["time_k1"] = trim($value->children($index)->plaintext);
                $index += 5;
                $studentInfo["time_k2"] = trim($value->children($index)->plaintext);
                $index++;
                $studentInfo["time_k3"] = trim($value->children($index)->plaintext);
                $index++;

                $studentInfo["status"] = trim($value->children($index)->plaintext);
                $this->insertStudent($studentInfo, $token);
            }
        }

        if ($ajaxReturn) {
            $this->success("成功同步");
        }
    }


    /**
     * 插入同步学员信息
     * @param $studentInfo
     */
    function insertStudent($studentInfo, $token = null)
    {
        // 性别
        switch ($studentInfo["sex"]) {
            case "男":
                $studentInfo["sex"] = "M";
                break;
            case "女":
                $studentInfo["sex"] = "W";
                break;
            default:
                $studentInfo["sex"] = "M";
                break;
        }

        // 查找已存在学员
        $model = M('student');
        $info = $model->where(array("token" => $token, "card_id" => $studentInfo["card_id"]))->find();


        // 课程
        $token || $token = get_token();
        $studentInfo["token"] = $token;
        $studentInfo["course_id"] = M('school_course')->where(array("token" => $token, "course" => array("like", "%" . $studentInfo["course_id"] . "%")))->find()['id'];

        // 报名时间
        $studentInfo["time_sign"] = strtotime($studentInfo["time_sign"]);

        // 教练
//        $studentInfo["teacher_k1"] = M('teacher')->where(array("token" => get_token(), "name" => array("like", "%" . $studentInfo["teacher_k1"] . "%")))->find()['id'];
//        $studentInfo["teacher_k2"] = M('teacher')->where(array("token" => get_token(), "name" => array("like", "%" . $studentInfo["teacher_k2"] . "%")))->find()['id'];


        //学时
//        $studentInfo["time_k1"] = $this->convertTimeToStamp($studentInfo["time_k1"]);
//        $studentInfo["time_k2"] = $this->convertTimeToStamp($studentInfo["time_k2"]);
//        $studentInfo["time_k3"] = $this->convertTimeToStamp($studentInfo["time_k3"]);


        // 转换状态
//        $statusMap = array("录入" => "0", "科目一通过" => "1", "科目二通过" => "2", "科目三通过" => "3", "结业" => "4", "逾期" => "-1");
//        $studentInfo["status"] = $statusMap[$studentInfo["status"]];
//        if (intval($info["status"]) > intval($studentInfo["status"])) {
//            unset($studentInfo["status"]);
//        }

        // 保存
        if (!empty($info)) {
            $studentInfo['id'] = $info['id'];
            unset($studentInfo["status"]);
            $model->save($studentInfo);
        } else {
            $studentInfo["status"] = "1";
            $model->add($studentInfo);
        }
    }

    /**
     * 模拟登录
     */
    function login_post($url, $cookie, $post)
    {
        $curl = curl_init();//初始化curl模块
        curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
        curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中
        curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
        curl_exec($curl);//执行cURL
        curl_close($curl);//关闭cURL资源，并且释放系统资源
    }

    /**
     * 请求post页面
     * @param $url
     * @param $cookie
     * @param $post
     * @return mixed
     */
    function get_post_content($url, $cookie, $post = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }

    /**
     * 登录成功后获取数据
     */
    function get_content($url, $cookie)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }

    function convertTimeToStamp($time)
    {
        // 计算小时
        $hour = 0;
        $indexHour = strpos($time, "小时");
        $indexMinuteBegin = 0;
        if ($indexHour > 0) {
            $hour = intval(substr($time, 0, $indexHour));
            $indexMinuteBegin = $indexHour + 7;
        }

        // 计算分钟
        $indexMinute = strpos($time, "分钟");
        $minute = intval(substr($time, $indexMinuteBegin, $indexMinute - $indexMinuteBegin));


        // 反悔
        return ($hour * 60 + $minute) * 60;
    }


    /**
     * 取得信息
     */
    public function getModelInfo()
    {
        // 查询模型信息
        $modelInfo = parent::getModelInfo(false);

        // 查询配置
        $conf = M('field_display_conf')->where(array("token" => get_token(), "status" => $_REQUEST["status"], "model" => "student"))->find()["value"];
        if (!empty($conf)) {
            $confFieldList = explode(",", $conf);

            for ($key = 0; $key < count($modelInfo["list_data"]["list_grids"]);) {
                $value = $modelInfo["list_data"]["list_grids"][$key]["field"][0];
                if (empty($value) || in_array(explode("|", $value)[0], $confFieldList)) {
                    $key++;
                    continue;
                } else {
                    array_splice($modelInfo["list_data"]["list_grids"], $key, 1);
                }
            }
        }

        // 返回
        $this->ajaxReturn($modelInfo);
    }

    /**
     * 取得学员数目
     */
    public function getStudentNum()
    {
        $result = $this->_get_model_list($this->model);
        $this->success("", null, null, $result["count"]);
    }

    /**
     * 判断操作是否出现
     * @param data 数据
     * @param opertaon 操作名称
     */
    protected function operationIsShow($data, $operation)
    {
        if ($operation == "绑定微信" && !empty($data["weixin_name"])) {
            return false;
        }

        if ($operation == "解绑微信" && empty($data["weixin_name"])) {
            return false;
        }

        if ($operation == "推荐费已支付" && (empty($data["in_name"]) || (!empty($data["in_name"] && $data["is_in_payed"] == "1")))) {
            return false;
        }

        return true;
    }

    /**
     * 文件导入
     */
    function fileImport()
    {
        $token = get_token();
        $Model = D(parse_name(get_table_name($this->model['id']), 1));
        if (!empty ($_FILES ['file_stu'] ['name'])) {

            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file_stu'] ['name']);
            $file_type = $file_types [count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower($file_type) != "xls") {
                $this->error('不是Excel文件，重新上传');
            }
            /*设置上传路径*/
            $savePath = SITE_PATH . '/Uploads';
            /*以时间来命名上传的文件*/

            $file_name = "_xueyuan_" . get_token() . "." . $file_type;
            /*是否上传成功*/
            if (!copy($tmp_file, $savePath . $file_name)) {
                $this->error('上传失败');
            }

            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($savePath . $file_name);
            $objWorksheet = $objPHPExcel->getSheet(0);
            $colNum = $objWorksheet->getHighestColumn();

            // 取得字段
            $fields = array();
            $fieldMap = array("学员姓名" => "name", "准考证明编号" => "exam_serial", "身份证明号码" => "card_id", "考试科目" => "status", "考试车型" => "course_id", "预约日期" => "subscribe_date", "约考日期" => "exam_date", "考试日期" => "exam_date", "考试成绩" => "exam_score", "考试场地" => "exam_place", "考试场次" => "exam_round");
            for ($j = 'A'; $j <= $colNum; $j++) {
                $value = $objWorksheet->getCell($j . '1')->getValue();
                if (!empty($value)) {
                    $fields[$j] = $fieldMap[$value];
                }
            }

            // 批量插入
            for ($i = 2; $i <= $objWorksheet->getHighestRow(); $i++) {
                $modelData = array();
                $modelData['token'] = $token;
                for ($j = 'A'; $j <= $colNum; $j++) {
                    $value = $objWorksheet->getCell($j . $i)->getValue();
                    if (!empty($fields[$j]) && !empty($value) && in_array($fields[$j], array("status", "exam_serial", "card_id", "exam_date", "exam_place", "exam_round", "exam_score"))) {
                        $modelData[$fields[$j]] = $this->convertField($fieldMap, $fields[$j], $value);
                    }

                    // 成绩
                    if ($fields[$j] == "exam_score") {
                        $modelData[$fields[$j]] = intval($modelData[$fields[$j]]);
                    }
                }

                // add the status prefix
                foreach ($modelData as $key => $value) {
                    if (strpos($key, "exam") > -1) {
                        $modelData[$key . "_" . $modelData["status"]] = $value;
                        unset($modelData[$key]);
                    }
                }


                // find and save
                $exist_student = $Model->where(array("token" => get_token(), 'card_id' => $modelData['card_id']))->find();
                if (!empty($exist_student)) {
                    // 设置通知状态
                    $notifyField = "exam_notify_" . $modelData["status"];
                    $dateField = "exam_date_" . $modelData["status"];
                    if ($exist_student[$dateField] < $modelData[$dateField]) {
                        $modelData[$notifyField] = "0";
                    }

                    // 根据成绩判断状态
                    if ($modelData["exam_score_" . $modelData["status"]] >= 90) {
                        $modelData["status"] = intval($modelData["status"]) + 1;
                        if ($modelData["status"] > 4) {
                            $modelData["status"] = 99;
                        }
                    }

                    // 保存
                    if ($modelData["status"] >= intval($exist_student["status"])) {
                        $modelData['id'] = $exist_student[id];
                        $Model->save($modelData);
                    }
                }
            }
        }

        // 返回
        $this->success();
    }

    /**
     *
     * 转换对应的栏位信息
     * @param $map
     * @param $field_name
     * @param $field_value
     */
    private function convertField($map, $field_name, $field_value)
    {
        // convert the value
        if (empty($map) || empty($field_name)) {
            return $field_value;
        }
        if (strpos($field_name, "date") > -1) {
            $field_value = strtotime($field_value);
        } else if ($field_name == "status") {
            $field_value = array("科目一" => 1, "科目二" => 2, "科目三" => 3, "科目四" => 4)[$field_value];
        } else if (in_array($field_name, array('time_sign', 'time_begin'))) {
            if (!empty($field_value)) {
                $field_value = (intval($field_value) - 70 * 365 - 19) * 86400 - 8 * 3600;
            }
        } else if (in_array($field_name, array('id_teacher_k2', 'id_teacher_k3', 'id_in_teacher'))) {
            $teacher = M('teacher')->where("name=\"" . $field_value . "\"")->find();
            if (empty($teacher)) {
                $field_value = null;
            } else {
                $field_value = $teacher['id'];
            }
        } else if (in_array($field_name, array('course_id'))) {
            $course = M('school_course')->where("name=\"" . $field_value . "\"")->find();
            if (empty($course)) {
                $field_value = null;
            } else {
                $field_value = $course['id'];
            }
        }

        // convert the value
        $convertValue = $map[$field_name][$field_value];
        if ($convertValue != "0" && empty($convertValue)) {
            return $field_value;
        } else {
            return $convertValue;
        }
    }

    /**
     * upload the excel
     */
    public function upload()
    {
        // param
        $token = get_token();

        $Model = D(parse_name(get_table_name($this->model['id']), 1));

        if (!empty ($_FILES ['file_stu'] ['name'])) {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file_stu'] ['name']);
            $file_type = $file_types [count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower($file_type) != "xls") {
                $this->error('不是Excel文件，重新上传');
            }
            /*设置上传路径*/
            $savePath = SITE_PATH . '/Uploads';
            /*以时间来命名上传的文件*/

            $file_name = "_xueyuan_" . get_token() . "." . $file_type;
            /*是否上传成功*/
            if (!copy($tmp_file, $savePath . $file_name)) {
                $this->error('上传失败');
            }


            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($savePath . $file_name);
            $objWorksheet = $objPHPExcel->getSheet(0);
            $colNum = $objWorksheet->getHighestColumn();
            $rowNum = $objWorksheet->getHighestRow();
            // 取得字段
            $fields = array();
            for ($j = 'A'; $j <= $colNum; $j++) {
                $value = $objWorksheet->getCell($j . '1')->getValue();
                if (!empty($value)) {
                    $fields[$j] = $value;
                }
            }

            // get the field map
            $fieldMap = $this->getFieldMap();

            // 批量插入
            for ($i = 3; $i <= $objWorksheet->getHighestRow(); $i++) {
                $modelData = array();
                $modelData['token'] = $token;
                for ($j = 'A'; $j <= $objWorksheet->getHighestColumn(); $j++) {
                    $value = $objWorksheet->getCell($j . $i)->getValue();
                    if (!empty($fields[$j]) && !empty($value)) {
                        $modelData[$fields[$j]] = $this->convertField($fieldMap, $fields[$j], $value);
                    }
                }

                $exist_student = $Model->where('card_id="' . $modelData['card_id'] . '"')->find();
                if (empty($exist_student)) {
                    $Model->add($modelData);
                } else {
                    $modelData['id'] = $exist_student[id];
                    $Model->save($modelData);
                }
            }
        }

        if (!empty($Model->getError())) {
            $this->error($Model->getError());
        } else {
            $num = $colNum;
            if ($num == null) {
                $num = 0;
            } else {
                $num = $rowNum - 2;
            }
            $this->success('添加' . $num . '个' . $this->model ['title'] . '成功！', U('lists?model=' . $this->model ['name'], $this->get_param));
        }
    }

    /**
     * 查询
     * @param bool|true $ajaxReturn
     * @param null $map
     * @return mixed
     */
    public function listsAdmin($ajaxReturn = true, $map = null)
    {
        // page and length
        $customActionName = i("customActionName");

        // if remove some data
        if ($customActionName == "notify") {
            $_POST['ids'] = $_POST['id'];
            unset($_REQUEST['id']);
            unset($_POST['id']);

            //通知
            $this->notifcation();
        }

        return parent::listsAdmin($ajaxReturn, $map); // TODO: Change the autogenerated stub
    }

    /**
     * 搜索条件
     * @param $model
     * @param $fields
     * @return array|string
     */
    public function _search_map($model, $fields)
    {
        $map = parent::_search_map($model, $fields);

        $where_str = "1 = 1 ";
        foreach ($map as $mapMame => $mapValue) {
            if(in_array($mapMame,array("name","card_id","phone"))){
                $where_str = $where_str . " and " . $mapMame . ' like "%' . $mapValue . '%" ';
            }else{
                $where_str = $where_str . " and " . $mapMame . ' = "' . $mapValue . '" ';
            }
        }
        $map = $where_str;


        // 最近考试
        $recent_exam = $_REQUEST["recent_exam"];
        if ($recent_exam == "1" || $recent_exam == "0") {
            $lastWeak = strtotime(date('Y-m-d', strtotime('-1 week')));
            $nextWeak = strtotime(date('Y-m-d', strtotime('+1 week')));

            if ($recent_exam == "1") {
                $map .= " and ( 1 != 1";
            } else {
                $map .= " and !( 1 != 1";
            }

            for ($index = 1; $index <= 4; $index++) {
                $map .= " or (" . $lastWeak . " <=  exam_date_" . $index . " and  exam_date_" . $index . " <= " . $nextWeak . ") ";
            }
            $map .= " )";
        }

        // 是否通知
        $exam_notify = $_REQUEST["exam_notify"];
        if ($exam_notify == "1" or $exam_notify == "0") {
            if ($exam_notify == "1") {
                $map .= " and ( 1 != 1";
            } else {
                $map .= " and !( 1 != 1";
            }

            for ($index = 1; $index <= 4; $index++) {
                $map .= " or ( status = " . $index . " and   exam_notify_" . $index . " =  '1' )";
            }
            $map .= " )";
        }

        // 返回
        return $map;
    }

    /**
     * 对选中学员进行通知操作
     * notifaction the user eaxm
     */
    public function notifcation()
    {
        // 判断模板
        $token = get_token();
        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $tempId = $db_config['temp_id'];
        !empty ($tempId) || $this->error('请配置消息模板ID!');


        // 判断选择数据
        $ids = i('ids');
        !empty ($ids) || $this->error('请选择要操作的数据!');

        // 查找驾校数据
        $Model = M('school');
        $map = array('token' => $token);
        $school_info = $Model->where($map)->find();

        // 查找学员
        $map = ' token = "' . get_token() . '" and id in ( ' . implode(",", $ids) . ' )';
        $dataList = M('student')->where($map)->select();
        foreach ($dataList as &$data) {
            //配置模板信息
            $tempData = array(
                'first' => array('value' => $data['name'] . '您好，您的考试已安排成功！', 'color' => '#173177'),
                'keyword1' => array('value' => '科目' . $data['status'], 'color' => "#173177"),
                'keyword2' => array('value' => date('Y-m-d', $data['exam_date_' . $data['status']]), 'color' => '#173177'),
                'keyword3' => array('value' => $data['exam_place_' . $data['status']], 'color' => '#173177'),
                'keyword4' => array('value' => $school_info['phone'], 'color' => '#173177'),
                'remark' => array('value' => "请提前1个小时到达考场，逾期以挂科处理。", 'color' => '#173177'),
            );

            // 发送信息
            $messageController = new MessageController();
            $logController = new LogController();
            if ($data['exam_notify_' . $data["status"]] != '1' && $return = $messageController->sendWeixinMessage($token, $data['openid'], $tempId, $tempData)) {
                if ($return['errcode'] == '0') {
                    $data['exam_notify_' . $data["status"]] = '1';
                    // 保存
                    M("student")->data($data)->save();
                    $logController->writeLog("学员进行考试通知，科目[" . $data["status"] . "]已发送通知!", LogMajorType::STUDENT, $data["id"]);
                } else {
                    $data['exam_notify_' . $data["status"]] = '0';
                    // 保存
                    M("student")->data($data)->save();
                    $logController->writeLog("学员进行考试通知，科目[" . $data["status"] . "]发送通知失败!", LogMajorType::STUDENT, $data["id"]);
                }
            }
        }
    }
}
