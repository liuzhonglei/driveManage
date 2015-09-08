<?php

namespace Addons\Student\Controller;


define ('SCHOOL_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Public');


class StudentController extends StudentBaseController
{
    /**
     * init
     */
    function _initialize()
    {

        $this->model = $this->getModel('student');
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
    public function lists($id_teacher = null)
    {
        // tips
        $normal_tips = '一般情况下请不要删除数据，会影响对应的与评价等相关数据。';
        $this->assign('normal_tips', $normal_tips);
        $top_more_button = array(array("is_buttion" => "0", "title" => "EXL导出", "url" => U("listsExcel")));
        $this->assign('top_more_button', $top_more_button);
        $this->assign('searchAction', "lists");

        // get data
        $list_data = $this->_get_model_list($this->model);

        // if referrer is student set in_name
        foreach ($list_data['list_data'] as &$data) {
            if ($data['intro_source'] == "2" && !empty($data['in_name'])) {
                $inStudentInfo = M('student')->where('token = "' . get_token() . '" and openid="' . $data["in_name"] . '"')->find();
                $data["in_name"] = $inStudentInfo['name'];
            }
        }

        // configure data
        $this->assign('file_button', 1);
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * download the studentl lists
     */
    public function listsExcel(){
        $this->model ['list_row'] = 100000;
        // get data
        $list_data = $this->_get_model_list($this->model);

        // if referrer is student set in_name
        foreach ($list_data['list_data'] as &$data) {
            if ($data['intro_source'] == "2" && !empty($data['in_name'])) {
                $inStudentInfo = M('student')->where('token = "' . get_token() . '" and openid="' . $data["in_name"] . '"')->find();
                $data["in_name"] = $inStudentInfo['name'];
            }
        }
        $this->downloadExcel($list_data);
    }

    /**
     * get the sign student data
     * @param bool $payed the student is payed
     */
    private function getSignData($payed = false)
    {
        // find the database
        $_REQUEST['status_name'] = '预约';
        $page = I('p', 1, 'intval'); // 默认显示第一页数据

        // 解析列表规则
        $list_data = $this->_list_grid($this->model);
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 搜索条件
        $map = $this->_search_map($this->model, $fields);
        if (is_array($map)) {
            $tempMap = "";
            foreach ($map as $name => $value) {
                $tempMap .= $name . " = \"" . $value . "\" and ";
            }
            $map = $tempMap;
        } else {
            $map .= " and ";
        }

        if ($payed) {
            $map .= "total_fee is not null";
        } else {
            $map .= "total_fee is null";
        }

        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];

        // 读取模型数据列表

        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');


        // special handle
        $name = "student_all";

        $datas = M($name)->field(empty ($fields) ? true : $fields)->where($map)->order('id desc')->page($page, $row)->select();
        // if referrer is student set in_name
        foreach ($datas as &$data) {
            if ($data['intro_source'] == "2" && !empty($data['in_name'])) {
                $inStudentInfo = M('student')->where('token = "' . get_token() . '" and openid="' . $data["in_name"] . '"')->find();
                $data["in_name"] = $inStudentInfo['name'];
            }
        }

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        $list_data ['list_data'] = $datas;

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $list_data ['_page'] = $page->show();
        }


        //return
        return $list_data;
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
        $list_data = $this->getSignData();

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


    /**
     * upload the excel
     */
    public function upload()
    {
        // param
        $token = get_token();


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
            $Model = D(parse_name(get_table_name($this->model['id']), 1));
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
     *
     * convert the file value
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

        // change the date
        if (in_array($field_name, array('time_sign', 'time_begin'))) {
            if (!empty($field_value)) {
                $field_value = (intval($field_value) - 70 * 365 - 19) * 86400 - 8 * 3600;
            }
        }

        // change the teacher
        if (in_array($field_name, array('id_teacher_k2', 'id_teacher_k3', 'id_in_teacher'))) {
            $teacher = M('teacher')->where("name=\"" . $field_value . "\"")->find();
            if (empty($teacher)) {
                $field_value = null;
            } else {
                $field_value = $teacher['id'];
            }
        }

        // change the course
        if (in_array($field_name, array('course_id'))) {
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
        $url = addons_url('Student://student/lists');
        redirect($url);
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
        $url = $_SERVER['HTTP_REFERER'];
        redirect($url);
    }

    /**
     * 模板变量赋值
     *
     * @access protected
     * @param mixed $name
     *            要显示的模板变量
     * @param mixed $value
     *            变量的值
     * @return Action
     */
    protected function assign($name, $value = '')
    {
        // set the teahcer data
        if ($name == 'fields') {
            $teacherData = $this->getData('teacher', array('status' => 1, 'token' => get_token()));
            $courseData = $this->getData('school_course', array('token' => get_token()));

            for ($i = 1; $i <= sizeof($value); $i++) {
                foreach ($value [$i] as &$vo) {
                    if (in_array($vo ['name'], array('id_teacher', 'id_teacher_k2', 'id_teacher_k3', 'id_in_teacher'))) {
                        $vo ['extra'] .= "\r\n" . $teacherData;
                    }
                    if ($vo ['name'] == 'course_id') {
                        $vo ['extra'] .= "\r\n" . $courseData;
                    }
                }
            }
        }

        // return
        return parent::assign($name, $value);
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
    private function getMyInfo()
    {
        $token = get_token();
        $openid = get_openid();
        $student = M('student_all')->where('openid= "' . $openid . '" and token = "' . $token . '"')->find();
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
                $inStudentInfo = M('student_all')->where('openid= "' . $in_student_openid . '" and token = "' . $token . '"')->find();
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
            if (!empty($erroMsg = $this->saveModel())) {
                $this->ajaxReturn(array("status" => "0", "info" => $erroMsg));
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
        $where = 'token = "'.$token.'" and openid = "'.$openid.'"';
        $queryParam = "";

        // current user is student or teacher
        $student = M("student")->where($where)->find();
        $teacher = M("teacher")->where($where)->find();
        if(!empty($student)){
            $queryParam = 't.in_student_openid="'.$openid.'"';
        }else if(!empty($teacher)){
            $queryParam = 't.id_in_teacher="'.$teacher['id'].'"';
        }else{
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
}
