<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

use Home\Controller\AddonsController;
use Think\Model;

define ( 'MOBILE_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Mobile' );
define ( 'MOBILE_PATH',"Addons://School@Mobile/");
class BaseController extends AddonsController
{

    static  $tableNames = array('student','student_question','student_banner','teacher','student_notification','qingqing_coupon');

    function _initialize()
    {
        parent::_initialize();

        $controller = strtolower(_CONTROLLER);

        $res ['title'] = '驾校信息';
        $res ['url'] = addons_url('School://School/lists');
        $res ['class'] = ($controller == 'school' || $controller == 'photo') ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '相关信息';
        $res ['url'] = addons_url('School://Info/lists');
        $res ['class'] = $controller == 'info' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '课程信息';
        $res ['url'] = addons_url('School://Course/lists');
        $res ['class'] = $controller == 'course' ? 'current' : '';
        $nav [] = $res;


        $res ['title'] = '驾校场地';
        $res ['url'] = addons_url('School://Place/lists');
        $res ['class'] = $controller == 'place' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '优惠资讯';
        $res ['url'] = addons_url('School://Privilege/lists');
        $res ['class'] = $controller == 'privilege' ? 'current' : '';
        $nav [] = $res;

        $this->assign('nav', $nav);

        // set if
        if(i('model') == 'teacher'){
            $this->assign('nav', null);
        }
    }


    // 通用插件的增加模型
    public function lists()
    {
        $_POST['token'] = get_token();
        parent::common_lists($this->model);
    }

    public function listsData(){
        $data = M('student')->query("select id,type,token from wp_school_info");

        foreach($data as &$vo){
            array_unshift($vo, '<input type="checkbox" name="id[]" value="'.$vo['id'].'">');//向数组插入元素
            $vo[1] = intval($vo[1]);
            $vo[2] = '12/09/2013';
           $vo[3] = "test";
        }

        $records = array();
        $status_list = array(
            array("success" => "Pending"),
            array("info" => "Closed"),
            array("danger" => "On Hold"),
            array("warning" => "Fraud")
        );
        $status = $status_list[rand(0, 2)];
        $records['data'] = $data;
        $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        $records["draw"] = intval($_REQUEST['draw']);
        $records["recordsTotal"] = 3;
        $records["recordsFiltered"] =  3;

        echo json_encode($records);
    }

    // 通用插件的增加模型
    public function add()
    {
        $_POST['token'] = get_token();
        parent::common_add($this->model);
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $_POST['token'] = get_token();
        parent::common_edit($this->model);
    }

    // 通用插件的删除模型
    public function del()
    {
        $_POST['token'] = get_token();
        parent::common_del($this->model);
    }


    // 获取模型列表数据
    public function _get_model_list($model = null, $page = 0, $order = 'id desc') {
        $page || $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据

        // 解析列表规则

        $list_data = $this->_list_grid ( $model );
        $grids = $list_data ['list_grids'];
        $fields = $list_data ['fields'];

        // 搜索条件
        $map = $this->_search_map ( $model, $fields );

        $row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];

        // 读取模型数据列表
        if ($model ['extend']) {
            $name = get_table_name ( $model ['id'] );
            $parent = get_table_name ( $model ['extend'] );
            $fix = C ( "DB_PREFIX" );

            $key = array_search ( 'id', $fields );
            if (false === $key) {
                array_push ( $fields, "{$fix}{$parent}.id as id" );
            } else {
                $fields [$key] = "{$fix}{$parent}.id as id";
            }

            /* 查询记录数 */
            $count = M ( $parent )->join ( "INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id" )->where ( $map )->count ();

            // 查询数据
            $data = M ( $parent )->join ( "INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id" )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( "{$fix}{$parent}.{$order}" )->page ( $page, $row )->select ();
        } else {
            empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );


            // special handle
            $name = get_table_name ( $model ['id'] );
            foreach(self::$tableNames as $handleName){
                if(!strcmp($handleName, $name)){
                    $name .= '_all';
                    break;
                }
            }
            //$name = parse_name ($name, true );
            $data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( $order )->page ( $page, $row )->select ();

            /* 查询记录总数 */
            $count = M ( $name )->where ( $map )->count ();
        }
        $list_data ['list_data'] = $data;

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $list_data ['_page'] = $page->show ();
        }

        return $list_data;
    }

    // 解析列表规则
    public function _list_grid($model) {
        $fields = array ();
        $grids = preg_split ( '/[;\r\n]+/s', htmlspecialchars_decode ( $model ['list_grid'] ) );
        foreach ( $grids as &$value ) {
            // 字段:标题:链接
            $val = explode ( ':', $value );
            // 支持多个字段显示
            $field = explode ( ',', $val [0] );
            $value = array (
                'field' => $field,
                'title' => $val [1]
            );
            if (preg_match ( '/^([0-9]*)%/', $val [1], $matches )) {
                $value ['title'] = str_replace ( $matches [0], '', $value ['title'] );
                $value ['width'] = $matches [1];
            }
            if (isset ( $val [2] )) {
                // 链接信息
                $value ['href'] = $val [2];
                // 搜索链接信息中的字段信息
                preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
                    $fields [] = $match [1];
                }, $value ['href'] );
            }
            if (strpos ( $val [1], '|' )) {
                // 显示格式定义
                list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
            }
            foreach ( $field as $val ) {
                $array = explode ( '|', $val );
                $fields [] = $array [0];
            }
        }
        // 过滤重复和错误字段信息
        $sign = ture;
        foreach(self::$tableNames  as $tableName){
            if(!strcmp($tableName, $model['name'])){
                $sign = false;
                break;
            }
        }
        if($sign){
        $model_fields = M ( 'attribute' )->where ( 'model_id=' . $model ['id'] )->field ( 'name' )->select ();
        $model_fields = getSubByKey ( $model_fields, 'name' );
        in_array ( 'id', $model_fields ) || array_push ( $model_fields, 'id' );
        $fields = array_intersect ( $fields, $model_fields );
        }
        $res ['fields'] = array_unique ( $fields );

        $res ['list_grids'] = $grids;
        return $res;
    }

    // add the search map
    public function _search_map($model, $fields) {
        $map='';


        foreach($_REQUEST as $name=>$vlaue) {
            if (strpos($name, ',') && !empty($vlaue)) {
                $where_str = " token = \"".get_token()."\" and ";
                $map = parent::_search_map($model,$fields);

                unset($map['token']);
                unset($map[$name]);
                // converth map
                foreach($map as $mapMame=>$mapValue){
                    $where_str =  $where_str.$mapMame.' = "'.$mapValue.'" and ';
                }


                // add the high search
                $where_str = $where_str.' (';
                $names = explode(',', $name);
                $values = explode(' ', $vlaue);
                foreach ($values as $fieldValue) {
                    $where_str = $where_str.' (';
                    foreach ($names as $field) {
                        if(!empty($field) && !empty($fieldValue)){
                            $where_str = $where_str. $field . ' like "%' . $fieldValue . '%" ' . 'or ' ;
                        }
                    }
                    if(strrchr($where_str,'or ') == 'or '){
                        $where_str = substr($where_str,0,sizeof($where_str)-4);
                    }
                    $where_str = $where_str.' ) and ';
                }

                if(strrchr($where_str,'and ') == 'and '){
                    $where_str = substr($where_str,0,sizeof($where_str)-5);
                }

                $where_str = $where_str.' ) ';
                $map = $where_str;

                // get out
                break;
            }
        }
        if(empty($map)){
            $map = parent::_search_map($model,$fields);
        }

        return $map;
    }


    /**
     * get the school info
     * @return [type] [description]
     */
    protected function  getSchoolInfo()
    {
        $Model = M($this->model['name']);
        $map = array('token' => get_token());
        $info = $Model->where($map)->find();
        return $info;
    }

}