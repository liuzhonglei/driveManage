<?php
namespace Addons\Teacher\Controller;

require_once(dirname(__FILE__) . '/../../QingQing/Public/Weixin/JSSDK.php');
use Addons\School\Controller\BaseController;


/**
 * Class TeacherController the teacher controller
 * @package Addons\Teacher\Controllere
 */
class TeacherController extends BaseController
{
    /**
     * init
     */
    function _initialize()
    {
        $this->model = $this->getModel('teacher');
        parent::_initialize();

        // 子导航
        $action = strtolower(_ACTION);
        $res ['title'] = '信息';
        $res ['url'] = addons_url('Teacher://teacher/lists');
        $res ['class'] = 'current';
        $nav [] = $res;

        $this->assign('nav', $nav);
    }

    /**
     *  通用插件的列表模型
     * @param null $id_teacher
     */
    public function lists($id_teacher = null)
    {
        // tips
        $normal_tips = '一般情况下请不要删除数据，会影响对应的学员与评价等相关数据。';
        $this->assign('normal_tips', $normal_tips);

        // get data
        $list_data = $this->_get_model_list($this->model);

        // assign the button
        $this->assign("top_more_button", array(array("is_button" => "1", "title" => "下载模板", "url" => ADDON_PUBLIC_PATH . "/demon.xls")));

        // configure data
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * binding the weixin code
     */
    function binding()
    {
        $param = array('teacher_id' => $_REQUEST['teacher_id']);
        $url = addons_url('Teacher://weixin/lists', $param);
        redirect($url);
    }

    /**
     * unbing the weixin code
     */
    function unbinding()
    {
        // change the data
        $Model = M('teacher');
        $data['openid'] = null;
        $Model->where('id=' . $_REQUEST['teacher_id'])->save($data);

        // show
        $url = addons_url('Teacher://teacher/lists');
        redirect($url);
    }

    /**
     * edit the photos
     */
    function photos()
    {
        $param = array('type' => '1', 'object_id' => $_REQUEST['object_id'], 'model' => 'teacher');
        $url = addons_url('Teacher://teacherPhoto/lists', $param);
        redirect($url);
    }


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

            $file_name = "_teacher_" . get_token() . "." . $file_type;
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

                $exist_teacer = $Model->where('name="' . $modelData['name'] . '" and phone="' . $modelData['phone'] . '"')->find();
                if (empty($exist_teacer)) {
                    $Model->add($modelData);
                } else {
                    $modelData['id'] = $exist_teacer[id];
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
     * create the student conf map
     */
    private function getFieldMap()
    {
        $field_maps = array();
        $fields = M('attribute')->query('select t.* from wp_attribute t left join wp_model t1 on t.model_id = t1.id where t1.name ="teacher"  and t.extra != "";');
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
        if (in_array($field_name, array('time_in'))) {
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
        if (in_array($field_name, array('area', 'subject'))) {
            $result = "";
            $field_value_list = explode(",", $field_value);
            foreach ($field_value_list as $field_single_value) {
                $convertValue = $map[$field_name][trim($field_single_value)];
                if (!empty($convertValue)) {
                    if (!empty($result)) {
                        $result .= ",";
                    }

                    $result .= $convertValue;
                }
            }

            return $result;
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
     * show the teacher rank (front)
     */
    function show()
    {
        // data
        $list = M('teacher')->query('select @x:=ifnull(@x,0)+1 as rank, t.* from wp_teacher_rank_all t where token="' . get_token() . '"');
        $this->assign('list', $list);

        // show
        $this->display(T('Addons://Teacher@Teacher/top'));
    }

    /**
     * get the teacher List
     * @return [type] [description]
     */
    function getTeacherList()
    {
        $token = get_token();
        $sql = <<<str
SELECT
    t1.*, t2.path,
    FROM_UNIXTIME(t1.time_in,"%Y-%m-%d") show_time_in,
    ifNull(t4.apprise_num,0) apprise_num,
    ifNull(t4.apprise_level,4) apprise_level, CASE t1. LEVEL
WHEN 1 THEN
    "一级教练"
WHEN 2 THEN
    "二级教练"
WHEN 3 THEN
    "三级教练"
END AS level_name,
    ifnull(t5.banner_number,0) banner_number
FROM
    wp_teacher t1
LEFT JOIN wp_picture t2 ON t1.photo = t2.id
LEFT JOIN (
    SELECT
        id_teacher,
        count(id) apprise_num,
        TRUNCATE (sum(LEVEL) / count(id), 0) apprise_level
    FROM
        wp_student_apprise t3
    GROUP BY
        t3.id_teacher
) t4 ON t4.id_teacher = t1.id
LEFT JOIN (
    SELECT
        teacher_id,
        count(id) banner_number
    FROM
        wp_student_banner
    GROUP BY
        teacher_id
) t5 ON t5.teacher_id = t1.id
where 1=1

str;

        $sql = $sql . 'and token ="' . get_token() . '" ';
        if (!empty($_POST['course_type']) && $_POST['course_type'] != '0') {
            $sql = $sql . 'and subject like "%' . $_POST['course_type'] . '%"';
        }
        if (!empty($_POST['search_name'])) {
            $sql = $sql . 'and name like "%' . $_POST['search_name'] . '%"';
        }

        if ($_POST['level_type'] == '0') {
            $sql = $sql . 'order by apprise_level desc';
        } else if ($_POST['level_type'] == '1') {
            $sql = $sql . 'order by apprise_num desc';
        } else if ($_POST['level_type'] == '2') {
            $sql = $sql . 'order by banner_number desc';
        }

        $select_data = M('teacher')->query($sql);
        // add url
        foreach ($select_data as &$vo) {
            $vo[info_url] = U('teacherPage', 'teacher_id=' . $vo[id]);
        }

        // return
        $this->ajaxReturn($select_data, 'JSON');
    }


    /**
     * get the teacher main info
     */
    function teacherPage()
    {
        $teacher_id = $_REQUEST['teacher_id'];
        $token = $this->getTeacherToken($teacher_id);
        get_token($token);
        $code_url = U('/home/Index/leaflets', 'token=' . get_token());
        $this->assign('code_url', $code_url);

        // teacher data
        $teacher_data = M('teacher')->where('id=' . $teacher_id)->find();
        switch ($teacher_data['level']) {
            case '1':
                $teacher_data['level_name'] = '一级教练 ';
                break;
            case '2':
                $teacher_data['level_name'] = '二级教练 ';
                break;
            case '3':
                $teacher_data['level_name'] = '三级教练 ';
                break;
            default:
                $teacher_data['level_name'] = '';
        }
        $this->assign('teacher_data', $teacher_data);

        // school data
        $schoolData = $this->getSchoolInfo();
        $this->assign('school_data', $schoolData);

        // phone
        if (!empty($teacher_data['phone'])) {
            $this->assign('phone', $teacher_data['phone']);
        } else {
            $this->assign('phone', $schoolData['phone']);
        }

        // photo data
        $imgs = M('school_photo')->query('select t1.*, t2.path from wp_school_photo t1 left join wp_picture t2 on t1.photo = t2.id where  t1.type="1" and t1.object_id = ' . $teacher_id . ' and t1.token="' . $token . '" order by t1.sort');
        if (sizeof($imgs) < 1) {
            $imgs[] = array('path' => MOBILE_PUBLIC_PATH . "/img/master_bg.png");
        }
        $this->assign('imgs', $imgs);

        // course
        $sql = 'select * from wp_school_course where token = "' . $token . '"';
        $course_data = M('school_course')->query($sql);
        $this->assign('course_data', $course_data);
        $this->assign('token', i("token"));

        // display
        $this->display(T(MOBILE_PATH . 'teacherPage'));
    }

    /**
     * get the school teacher data
     */
    function getTeachers()
    {
        $list = M('teacher')->query('select id, name text from wp_teacher t where t.token="' . get_token() . '" and status="1"');
        $this->ajaxReturn($list);
    }

    /**
     * show the teacher page
     */
    function getTeacherMainInfo()
    {
        $teacher_id = $_REQUEST['teacher_id'];
        $token = $this->getTeacherToken($teacher_id);

        // teacher
        $teacher_data = M('teacher')->where('id="' . $teacher_id . '" and token="' . $token . '"')->find();
        if ($teacher_data['subject']) {
            $course_names = '';
            $courses = explode(',', $teacher_data['subject']);
            foreach ($courses as $vo) {
                $course_names = $course_names . ' 科目' . $vo;
            }

            $teacher_data['course_names'] = $course_names;
        }
        $teacher_data['area_name'] = get_name_by_status($teacher_data['area'], 'area', $this->getModel('teacher')['id']);
        $this->assign('teacher_data', $teacher_data);

        // course
        $sql = 'select * from wp_school_course where token = "' . $token . '"';
        $course_data = M('school_course')->query($sql);
        $this->assign('course_data', $course_data);

        // banner
        $bannerList = M('student_banner')->where('teacher_id=' . $teacher_id)->select();
        $banner_number = count($bannerList);
        $this->assign('banner_number', $banner_number);

        // the activity
        $payItme = M("school_payitem")->where("type='activity' and token = '" . $token . "'")->find();
        $this->assign("payItme",$payItme);

        // display
        $this->display(T(MOBILE_PATH . 'teacherPageInfo'));

    }

    function  getTeacherSchool()
    {

        $teacher_id = $_REQUEST['teacher_id'];
        $token = $this->getTeacherToken($teacher_id);
        $this->assign($this->getSchoolInfo());
        $data = M('school_info')->where('token = "' . $token . '" and type = 2')->find();
        $this->assign($data);
        $this->display(T(MOBILE_PATH . 'teacherPageSchool'));
    }

    /**
     * display the teacher register page
     */
    function  getTeacherReg()
    {
        // param
        $teacher_id = $_REQUEST['teacher_id'];
        $token = $this->getTeacherToken($teacher_id);
        $studentInfo = $this->getMyInfo();

        // assign
        $this->assign("studentInfo", $studentInfo);
        if (!empty($studentInfo['course_id'])) {
            $course_data = M('school_course')->where('token = "' . $token . '" and id = "' . $studentInfo['course_id'] . '"')->find();
            $this->assign($course_data);
        } else  if (empty($_REQUEST['id'])) {
            $course_data = M('school_course')->where('token = "' . $token . '"')->find();
            $this->assign($course_data);
        } else {
            $this->assign($_REQUEST);
        }


        if (!empty($studentInfo) && ((!empty($studentInfo['total_fee']) && $studentInfo['status'] == "-1") || $studentInfo['status'] != "-1")) {
            $this->assign("signHide", "hidden");
            $this->assign("signText", "您已经在本驾校登记过了，请耐心等待通知！");
        } else {
            $this->assign("signHide", "");
        }

        // the activity
        $payItme = M("school_payitem")->where("type='activity' and token = '" . $token . "'")->find();
        $this->assign("payItme",$payItme);

        // display
        $this->display(T(MOBILE_PATH . 'teacherPageRegister'));
    }

    function getTeacherApprise()
    {
        $teacher_id = $_REQUEST['teacher_id'];
        $token = $this->getTeacherToken($teacher_id);
        $number = $_REQUEST['load_number'];
        $teacher_id = $_REQUEST['teacher_id'];
        $sql = 'select t.*,FROM_UNIXTIME(t.time) format_time, t1.name show_name ,t2.headimgurl headimgurl from wp_student_apprise t left join  wp_student t1 on t.id_student = t1.id left join wp_follow  t2 on t1.openid = t2.openid where t.token = "' . $token . '" and t.id_teacher = ' . $teacher_id . ' limit ' . $number;
        $apprise_data = M('student_apprise')->query($sql);
        $this->ajaxReturn($apprise_data, 'JSON');
    }


    function  getTeacherReserve()
    {
        $this->display(T(MOBILE_PATH . 'teacherPageReserve'));
    }


    /**
     * get my teacher data
     */
    function getMyStudent()
    {
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
order by t.id desc
str;
        $sql = $sql . " where t1.openid=\"" . $openid . "\" and t.status in (\"-1\",\"0\",\"1\",\"2\",\"3\",\"4\")";
        $data = M('student')->query($sql);
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * get the all teacher data
     */
    function getNavigatorTeachers()
    {
        // connect the sql
        $sql = <<<str
select t.*,t1.name school_name, t2.path ,t4.apprise_level from wp_teacher t left join wp_school t1 on t.token = t1.token
LEFT JOIN wp_picture t2 ON t.photo = t2.id
LEFT JOIN (
	SELECT
		id_teacher,
		count(id) apprise_num,
		TRUNCATE (sum(LEVEL) / count(id), 0) apprise_level
	FROM
		wp_student_apprise t3
	GROUP BY
		t3.id_teacher
) t4 ON t4.id_teacher = t.id
where 1=1
and t.position != ""
str;
        $select_data = M('teacher')->query($sql);

        // add url
        foreach ($select_data as &$vo) {
            $vo[info_url] = U('teacherPage', array('teacher_id' => $vo[id], 'token' => constant("QINGQING_TOKEN")));
        }

        $this->ajaxReturn($select_data);
    }

    /**
     * show the master flag
     */
    function teacherBannerList()
    {
        // param
        $openid = get_openid();
        $token = get_token();

        $sql = <<<str
SELECT
	t.fee,
	FROM_UNIXTIME(t.time, "%Y年%m月%d日") day,
	t1. NAME teacher_name,
	t2. NAME student_name
FROM
	(select * from wp_student_banner where token = "$token") t
INNER JOIN (
            SELECT
            *
            FROM
		wp_teacher
	WHERE
		openid = "$openid"
) t1 ON t.teacher_id = t1.id
left JOIN (select * from wp_student where token = "$token") t2 ON t.student_id = t2.id
order by t.id desc
str;
        $data = M('student_banner')->query($sql);
        $this->assign('data', $data);
        $this->assign('data_size', count($data));
        $this->display(T(MOBILE_PATH . 'teacherCenterBannerList'));
    }

    /**
     * show the master comment
     */
    function teacherAppriseList()
    {
        // param
        $openid = get_openid();
        $token = get_token();

        $sql = <<<str
SELECT
	t.*,
	FROM_UNIXTIME(t.time, "%Y年%m月%d日") day,
	t1. NAME teacher_name,
	t2. NAME student_name
FROM
	(select * from wp_student_apprise where token = "$token") t
INNER JOIN (
            SELECT
            *
            FROM
		wp_teacher
	WHERE
		openid = "$openid"
) t1 ON t.id_teacher = t1.id
left JOIN (select * from wp_student where token = "$token") t2 ON t.id_student = t2.id
order by id desc
str;
        $data = M('student_apprise')->query($sql);
        $this->assign('data', $data);
        $this->assign('data_size', count($data));
        $this->display(T(MOBILE_PATH . 'teacherCenterAppriseList'));
    }


    /**
     * set the teacher token
     * @param $teacherId
     */
    protected function  getTeacherToken($teacherId)
    {
        $info = M('teacher')->where('id=' . $teacherId)->find();
        get_token($info['token']);
        return $info['token'];
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
     * school register
     * @return the page
     */
    function register()
    {
        $teacher_id = $_REQUEST['id_in_teacher'];
        $token = $this->getTeacherToken($teacher_id);

        if (IS_POST) {
            $student = $this->getMyInfo();
            if (!empty($student)) {
                $this->ajaxReturn(array("status" => "0", "info" => "当前微信号已注册，请等待通知！"));
            }

            $_POST['token'] = $token;
            $_POST['status'] = '-1';
            $_POST['intro_source'] = i('intro_source');
            $_POST['time_sign'] = date("Y-m-d");
            $_POST['openid'] = get_openid();
            $current_token = get_token();
            if (empty($_POST['intro_source']) && $current_token == constant("QINGQING_TOKEN")) {
                $_POST['intro_source'] = "5";
            }

            if (empty($_POST['intro_source']) && !empty($_POST['id_in_teacher'])) {
                $_POST['intro_source'] = '1';
            }

            if (empty($_POST['intro_source'])) {
                $_POST['intro_source'] = '0';
            }
            parent::common_add($this->getModel('student'));
        }
    }

    /**
     * return the page attention
     */
    function teacherSignPackage()
    {
        $appinfo = get_token_appinfo("gh_94ecad95d624");
        $jssdk = new \JsSdk($appinfo["appid"], $appinfo["secret"]);
        $signPackage = $jssdk->GetSignPackage($_SERVER['HTTP_REFERER']);
        $this->ajaxReturn($signPackage);
    }

}

;
