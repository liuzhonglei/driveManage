<?php

namespace Addons\School\Controller;
use Home\Controller\AddonsController;

class PhotoController extends BaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_photo' );
        parent::_initialize ();

        // son navitor
        $action = strtolower ( _ACTION );
        $res ['title'] = '信息';
        $res ['url'] = addons_url ( 'School://school/lists' );
        $res ['class'] = '';
        $nav [] = $res;

        $res ['title'] = '首页幻灯片';
        $res ['url'] = addons_url ( 'School://photo/lists' );
        $res ['class'] =  'cur';
        $nav [] = $res;
        $this->assign ( 'sub_nav', $nav );

    }

    // 通用插件的列表模型
    public function lists() {
        $map ['token'] = get_token ();
        $map ['type'] = '0';
        $map ['object_id'] = $_REQUEST['object_id'];
        session ( 'common_condition', $map );

        $list_data = $this->_get_model_list ( $this->model );
        foreach ( $list_data ['list_data'] as &$vo ) {
            $vo ['photo'] = '<img src="' . get_cover_url ( $vo ['photo'] ) . '" width="50px" >';
        }
        $this->assign ( $list_data );


        // add param
        $get_param = array('type'=>'0', 'object_id'=>i('object_id'));
        $this->assign('get_param',$get_param);

        // display
        $templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
        $this->display ( $templateFile );
    }

    function add(){
        $this->assign('type','0');
        $this->assign('object_id',i('object_id'));
        parent::add();
    }
}
