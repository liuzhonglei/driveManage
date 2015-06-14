<?php

namespace Addons\Student\Controller;
require_once('Excel/reader.php');

define ( 'SCHOOL_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Public' );

class StudentController extends StudentBaseController
{
    /**
     * init
     */
    function _initialize()
    {
        $this->model = $this->getModel('student');
        parent::_initialize();

        // 子导航
        $action = strtolower(_ACTION);
        $res ['title'] = '信息';
        $res ['url'] = addons_url('Student://student/lists');
        $res ['class'] = 'cur';
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
        $this->assign ( 'normal_tips', $normal_tips );

        // get data
        $list_data = $this->_get_model_list($this->model);

        // configure data
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * upload the excel
     */
    public function upload()
    {
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

            // ExcelFile($filename, $encoding);
            $excleReader = new Spreadsheet_Excel_Reader();


            // Set output Encoding.
            $excleReader->setOutputEncoding('GBK');

            error_reporting(E_ALL ^ E_NOTICE);
            $Model = D(parse_name(get_table_name($this->model['id']), 1));
            // 取得字段
            $fields = array();
            for ($j = 1; $j <= $excleReader->sheets[0]['numCols']; $j++) {
                $value = iconv('gbk', 'utf-8', $excleReader->sheets[0]['cells'][1][$j]);
                if (!empty($value)) {
                    $fields[$j] = $value;
                }
            }

            // 批量插入
            for ($i = 3; $i <= $excleReader->sheets[0]['numRows']; $i++) {
                $modelData = array();
                for ($j = 1; $j <= $excleReader->sheets[0]['numCols']; $j++) {
                    $value = iconv('gbk', 'utf-8', $excleReader->sheets[0]['cells'][$i][$j]);
                    if (!empty($fields[$j])) {
                        $modelData[$fields[$j]] = $value;
                    }
                }
                $modelData['token'] = get_token();

                // 获取模型的字段信息
                if ($Model->create($modelData) && $id = $Model->add($modelData)) {
                    $this->_saveKeyword($this->model, $id);

                    $this->success('添加' . $this->model ['title'] . '成功！', U('lists?model=' . $this->model ['name'], $this->get_param));
                } else {
                    $this->error($Model->getError());
                }
            }

        }
    }

    // binding the weixin code
    function binding()
    {
        $param = array('student_id' => $_REQUEST['student_id']);
        $url = addons_url('Student://weixin/lists', $param);
        redirect($url);
    }

    // unbingding the weixin code
    function unbinding(){
        // change the data
        $Model = M('student');
        $data['openid'] = null;
        $Model->where('id='.$_REQUEST['student_id'])->save($data);

        // show
        $url = addons_url('Student://student/lists');
        redirect($url);
    }

    /**
     * 模板变量赋值
     *
     * @access protected
     * @param mixed $name
     *        	要显示的模板变量
     * @param mixed $value
     *        	变量的值
     * @return Action
     */
    protected function assign($name, $value = '') {
        // set the teahcer data
        if($name == 'fields'){
            $teacherData = $this->getData('teacher',array('status'=>1, 'token'=>get_token()));
            $courseData =  $this->getData('school_course',array('token'=>get_token()));

           for( $i =1; $i <= sizeof($value); $i++){
                foreach ($value [$i] as &$vo) {
                    if ($vo ['name'] == 'id_teacher' || $vo ['name'] == 'id_teacher_k2' || $vo ['name'] == 'id_teacher_k3') {
                        $vo ['extra'] .= "\r\n" . $teacherData;
                    }
                    if ($vo ['name'] == 'course_id') {
                        $vo ['extra'] .= "\r\n" . $courseData;
                    }
                }
            }
        }

        // return
        return parent::assign( $name, $value );
    }

    /**
     * get the model value
     * @param $modelName
     * @param $map
     * @return string id:value
     */
    function getData($modelName,$map){
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
            $sql = 'select t.*, t1.headimgurl from wp_teacher t left join wp_follow t1 on t.openid = t1.openid and t.token = t1.token where t.token = "' . $token . '" and t.id in (' . $ids . ')';
            $teacherList = M('teacher')->query($sql);
        }
        $this->assign('teacherList', $teacherList);
        $this->assign('student', $student);


        // show
        $this->display(T('Addons://School@School/showMaster'));
    }

    /**
     * show comment teacher page
     */
    function commentTeacher()
    {
        // param
        $token = get_token();
        $Model = M('student_apprise');

        $teacher_id = i('teacher_id',0,'intval');
        $student_id = i('student_id',0,'intval');

        if (IS_POST) {
            $level = i('value',0,'intval');
            $apprise = i('comment');

            $time = time();

            $data = $Model->where(array('teacher_id'=>$teacher_id,'student_id'=>$student_id,'token'=>$token))->find();
            $result = true;
            if(!empty($data) && $data['level'] == $level && $data['apprise'] == $apprise){
                $result = true;
            } else if($data){
                $data['level'] = $level;
                $data['apprise'] = $apprise;
                $result = $Model->save($data);
            }else{
                $data = array('teacher_id'=>$teacher_id,'student_id'=>$student_id,'level'=>$level,'apprise'=>$apprise,'time'=>$time);
                $result = $Model->add($data);
            }

            if($result){
                $return = array('status'=>'0');
            }else{
                $return = array('status'=>'-1','error'=>$Model->getDbError());
            }
            $this->ajaxReturn($return);
        }else{
            $this->assign($_GET);
            $teacher_id = i('teacher_id');
            $sql = 'select t.*, t1.headimgurl from wp_teacher t left join wp_follow t1 on t.openid = t1.openid and t.token = t1.token where t.token = "' . $token . '" and t.id = "' . $teacher_id . '"';
            $teacher =  M('teacher')->query($sql)[0];
            $this->assign('teacher',$teacher);

            $data = $Model->where(array('teacher_id'=>$teacher_id,'student_id'=>$student_id,'token'=>$token))->find();
            $this->assign('data',$data);

            // show
            $this->display(T('Addons://School@School/comment'));
        }
    }

    /**
     * show the binding search teacher page
     */
    function searachBindTeacher(){
        // param

        $token = get_token();

        if(IS_POST){
            $name = i('name');
            $sql = 'select t.*, t1.headimgurl from wp_teacher t left join wp_follow t1 on t.openid = t1.openid and t.token = t1.token where t.token = "' . $token . '" and t.name like "%' . $name . '%"';
            $teacherList = M('teacher')->query($sql);
            $this->ajaxReturn($teacherList);
        }else{
            $this->display(T('Addons://School@School/bindMaster'));
        }
    }

    /**
     * student binding the teacher
     */
    function bindTeacher(){
        // param
        $teacherId = i('teacherId');
        $openid = get_openid();
        $token = get_token();
        $Model = M('student');

        // get student
        $student = M('student')->where('openid="'.$openid.'" and token = "'.$token.'"')->find();

        // check
        $student || $this->ajaxReturn(array('status'=>'-1','error'=>'微信没有绑定学员！'));
        if(!empty($student['id_teacher_k2']) && !empty($student['id_teacher_k3'])){
            $this->ajaxReturn(array('status'=>'-1','error'=>'学员已经绑定所有教练，需要修改请于驾校联系！'));
        }

        // modify and save
        if(empty($student['id_teacher_k2'])){
            $student['id_teacher_k2'] = $teacherId;
        }else if(empty($student['id_teacher_k3'])){
            $student['id_teacher_k3'] = $teacherId;
        }
        $Model->save($student);

        // return
        $this->ajaxReturn(array('status'=>'0'));

    }
}
