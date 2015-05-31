<?php

namespace Addons\Student\Controller;
require_once('Excel/reader.php');

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
        $where_str = '';
        foreach($_REQUEST as $name=>$vlaue) {
            if (strpos($name, ',')) {
                $names = explode(',', $name);
                $values = explode(' ', $vlaue);
                foreach ($names as $field) {
                    foreach ($values as $fieldValue) {
                        $where_str = $where_str . 'or ' . $field . ' like "%' . $fieldValue . '%"';
                    }
                }
            }
        }

        // 使用提示
        $map ['token'] = get_token();
        session('common_condition', $map);

        // condition
        $_POST['token'] = get_token();

        // get data
        $list_data = $this->_get_model_list($this->model);

        // teacher data
        $list_data = $this->replaceTeacerId($list_data, 'id_teacher');
        $list_data = $this->replaceWeixin($list_data, 'open_id');

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


    // 通用插件的编辑模型
    public function edit()
    {
        $model = $this->model;
        $id = I('id');

        if (IS_POST) {
            $Model = new  \Addons\Student\Model\StudentModel();

            $Model->checkWeixin($_POST['open_id']);
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $model ['id']);
            if ($Model->validate()->create() && $Model->save()) {
                $this->success('保存' . $model ['title'] . '成功！', U('lists?model=' . $model ['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model ['id']);

            $extra = $this->getTeaData();
            if (!empty ($extra)) {
                foreach ($fields [1] as &$vo) {
                    if ($vo ['name'] == 'id_teacher' || $vo ['name'] == 'id_in_teacher') {
                        $vo ['extra'] .= "\r\n" . $extra;
                    }
                }
            }

            // 获取数据
            $data = M(get_table_name($model ['id']))->find($id);
            $data || $this->error('数据不存在！');

            $token = get_token();
            if (isset ($data ['token']) && $token != $data ['token'] && defined('ADDON_PUBLIC_PATH')) {
                $this->error('非法访问！');
            }

            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $model ['title'];

            $this->display('edit');
        }
    }

    // binding the weixin code
    function binding()
    {
        $param = array('student_id' => $_REQUEST['student_id']);
        $url = addons_url('Student://weixin/lists', $param);
        redirect($url);
    }

    // 获取所属分类
    function getTeaData()
    {
        $extra = '';
        $map ['status'] = 1;
        $map ['token'] = get_token();
        $list = M('teacher')->where($map)->select();
        foreach ($list as $v) {
            $extra .= $v ['id'] . ':' . $v ['name'] . "\r\n";
        }
        return $extra;
    }
}
