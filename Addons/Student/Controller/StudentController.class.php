<?php

namespace Addons\Student\Controller;
use Addons\School\Controller\Common\Log\LogController;
use Addons\School\Controller\Common\Log\LogMajorType;
use Addons\EO2OPayment\Controller\EO2OPaymentController;


define ('SCHOOL_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Public');

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

        // configure data
        $this->assign('file_button', 1);
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * search the students
     * @param $text
     * @param $fieldResult
     * @return mixed
     */
    public function getStudentOpenids($text, $fieldResult)
    {
        $fields = array("name", "phone");
        $sql = "select " . $fieldResult . " from wp_student t where t.token = '" . get_token() . "' and (";
        $fieldMap = "";
        foreach ($fields as $field) {
            if (empty(!$fieldMap)) {
                $fieldMap .= "or ";
            }
            $fieldMap .= $field . " like '%" . $text . "%' ";
        };
        $sql .= $fieldMap . " )";
        return M('student')->query($sql);
    }

    /**
     * search the follow
     * @param $text
     * @param $fieldResult
     * @return mixed
     */
    public function getFollowOpneids($text, $fieldResult)
    {
        $fields = array("nickname");
        $sql = "select " . $fieldResult . " from wp_follow t where t.token = '" . get_token() . "' and ( ";
        $fieldMap = "";
        foreach ($fields as $field) {
            if (empty(!$fieldMap)) {
                $fieldMap .= "or ";
            }
            $fieldMap .= $field . " like '%" . $text . "%' ";
        };
        $sql .= $fieldMap . " )";
        return M('follow')->query($sql);
    }

    /**
     * get the
     * @return string
     */
    private function getPayOpenid()
    {
        $payed = $_REQUEST['payed'];
        $result = "";
        if ($payed !== null) {
            $sql = "select openid from wp_eo2o_payment_count t where t.token = '" . get_token() . "' and  total_fee is not null";
            $records = M('eo2o_payment_count')->query($sql);
            if (count($records) > 0) {
                if ($payed) {
                    $result = "and openid in ( " . $this->getArrayStr($records) . " )";
                } else {
                    $result = "and openid not in ( " . $this->getArrayStr($records) . " )";
                }
            } else {
                if ($payed) {
                    $result = "1 = 2";
                } else {
                    $result = " ";
                }
            }

        }

        // return
        return $result;

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
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 搜索条件
        $fieldValue = "";
        foreach ($_REQUEST as $name => $value) {
            if (strpos($name, ',')) {
                $fieldValue = $value;
            }
        }

        $map = "t.token = '" . get_token() . "'" . $this->getPayOpenid();;
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
     * create the student execute sql
     * @param $fields
     * @param $map
     * @param $page
     * @param $row
     * @param $order
     * @return string
     */
    private function createStudentSql($fields, $map, $page, $row, $order)
    {
        $token = get_token();
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
            $student = M('student')->where('id = '.$id)->find();
            if(!empty($student)){
                $status = $_REQUEST['status'];
                if($status != $student['status']){
                    $logController = new   LogController();
                    $originalStatus = get_name_by_status($student['status'],'status',$this->model['id']);
                    $currentStatus = get_name_by_status($status,'status',$this->model['id']);
                    $logController->writeLog("学员状态发生变化，状态[".$originalStatus."]->状态[".$currentStatus."]",LogMajorType::STUDENT,$id);
                }
            }
        }


        //修改
        parent::edit();
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

    /**
     * 查看学员的详情
     */
    function detail(){

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
        if($this->isAdmin()){
            $this->success();
        }else{
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
        if($this->isAdmin()){
            $this->success();
        }else{
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
    private function getMyInfo()
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
     * convert  the records to condition str
     * @param $array
     * @return string
     */
    private function  getArrayStr($array)
    {
        $return = "";
        foreach ($array as $record) {
            if (!empty($return)) {
                $return .= ",";
            }
            foreach ($record as $key => $value) {
                $return .= "'" . $value . "'";
                break;
            }

        }
        return $return;
    }

    /**
     * 取得驾校的所有学员
     */
    public function  getStudents(){
        $list = M('student')->query('select id, name text from wp_student t where t.token="' . get_token() . '"');
        $this->ajaxReturn($list);
    }

    /**
     * 增加学员的划款流水
     */
    function  moneyLogAdd(){
        $where = 'id  = ' .  $_REQUEST['student_id'] . '';
        $student = M("student")->where($where)->find();
        $_REQUEST['openid'] = $student['openid'];

        $paymentController = new EO2OPaymentController();

        if($this->isAdmin()){
            $paymentController->addAdmin();
        }else{
            $nav = $this->get('nav');
            $subNav = $this->get('sub_nav');
            $paymentController->add($nav,$subNav);
        }
    }
}
