<?php

namespace Addons\Student\Controller;
require_once('Excel/reader.php');

/**
 * 获取属性信息并缓存
 *
 * @param integer $id
 *            属性ID
 * @param string $field
 *            要获取的字段名
 * @return string 属性信息
 */
function get_model_attribute($model_id, $group = true)
{

}

class StudentController extends StudentBaseController
{
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

    // 通用插件的列表模型
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

    // 上传
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
            //$data->setUTFEncoder('mb');
            //  $data->setUTFEncoder('GBK');
            /***
             * if you want you can change 'iconv' to mb_convert_encoding:
             * $data->setUTFEncoder('mb');
             *
             **/

            /***
             * By default rows & cols indeces start with 1
             * For change initial index use:
             * $data->setRowColOffset(0);
             *
             **/


            /***
             *  Some function for formatting output.
             * $data->setDefaultFormat('%.2f');
             * setDefaultFormat - set format for columns with unknown formatting
             *
             * $data->setColumnFormat(4, '%.3f');
             * setColumnFormat - set format for column (apply only to number fields)
             *
             **/

            $excleReader->read($savePath . $file_name);

            /*


             $data->sheets[0]['numRows'] - count rows
             $data->sheets[0]['numCols'] - count columns
             $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

             $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell

                $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
                    if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
                $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
                $data->sheets[0]['cellsInfo'][$i][$j]['colspan']
                $data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
            */

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
                    if ($vo ['name'] == 'id_teacher' || $vo ['name'] == 'id_in_teacher') {
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

    function getData($modleName,$map){
        $extra = ':' . "\r\n";
        $list = M($modleName)->where($map)->select();

        foreach ($list as $v) {
            $extra .= $v ['id'] . ':' . $v ['name'] . "\r\n";

        }
        return $extra;
    }
}
