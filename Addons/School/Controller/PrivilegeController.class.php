<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 11:14
 */

namespace Addons\School\Controller;


class PrivilegeController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_privilege' );
        parent::_initialize ();
    }


    // 通用插件的列表模型
    public function lists() {
        // check
        $map ['token'] = get_token ();
        session ( 'common_condition', $map );

        // condition
        $_POST['token'] = get_token ();
        $select_data = parent::_get_model_list($this->model);
        $this->assign($select_data);

        //设置显示控件
        $this->assign('del_button','0');
        $this->assign('search_button','0');
        $this->assign('check_all','0');
        if(sizeof($select_data['list_data']) > 0){
            $this->assign('add_button','0');
        }

        // display
        $this->display ( "lists" );
    }

    // 详情
    function detail() {
        $map ['id'] = I ( 'id', 0, 'intval' );
        $info = M ( $this->model['name'] )->where ( $map )->find();
        $this->assign ( 'info', $info );

        $this->display ('detail');
    }

    // 详情
    function mainPri() {
        $Model = M ( $this->model['name']);
        $info = $Model->find();
        $this->assign ( 'info', $info );
        $this->display ( 'detail' );
    }

}