<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

use Home\Controller\AddonsController;


class BaseController extends AddonsController
{

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
    }

//
//    // 通用插件的增加模型
//    public function lists()
//    {
//        $_POST['token'] = get_token();
//        parent::common_lists($this->model);
//    }
//
//    // 通用插件的增加模型
//    public function add()
//    {
//        $_POST['token'] = get_token();
//        parent::common_add($this->model);
//    }
//
//    // 通用插件的编辑模型
//    public function edit()
//    {
//        $_POST['token'] = get_token();
//        parent::common_edit($this->model);
//    }
//
//    // 通用插件的删除模型
//    public function del()
//    {
//        $_POST['token'] = get_token();
//        parent::common_del($this->model);
//    }

    // add the search map
    public function _search_map($model, $fields) {
        $map='';

        foreach($_REQUEST as $name=>$vlaue) {
            if (strpos($name, ',') && !empty($vlaue)) {
                $map = parent::_search_map($model,$fields);

                $where_str = " 1 = 1 and ";
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
                foreach ($names as $field) {
                    foreach ($values as $fieldValue) {
                        if(!empty($field) && !empty($fieldValue)){
                            $where_str = $where_str. $field . ' like "%' . $fieldValue . '%" ' . 'or ' ;
                        }
                    }
                }

                if(strrchr($where_str,'or ') == 'or '){
                    $where_str = substr($where_str,0,sizeof($where_str)-4);
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
}