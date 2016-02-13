<?php

namespace Addons\School\Controller;

class PhotoController extends SchoolBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'school_photo' );
        parent::_initialize ();
        $controller = strtolower(_CONTROLLER);

        // son navitor
        $origin_url = '';
        if($_REQUEST['type'] == '0'){
            $origin_url = addons_url ( 'School://school/lists' );
        }else{
            $origin_url =  addons_url ( 'Teacher://teacher/lists' );
        }

        $res ['title'] = '信息';
        $res ['url'] = $origin_url;
        $res ['class'] = '';
        $nav [] = $res;

        $res ['title'] = '首页幻灯片';
        $res ['url'] = addons_url ( 'School://photo/lists' );
        $res ['class'] = 'cur';
        $nav [] = $res;
        $this->assign ( 'sub_nav', $nav );

    }

    // 通用插件的列表模型
    public function lists() {
        $map ['token'] = get_token ();
        $map ['type'] = $_REQUEST['type'];
        $map ['object_id'] = $_REQUEST['object_id'];
        session ( 'common_condition', $map );

        // change the photo
        $list_data = $this->_get_model_list ( $this->model );
        foreach ( $list_data ['list_data'] as &$vo ) {
            $vo ['photo'] = '<img src="' . get_cover_url ( $vo ['photo'] ) . '" width="50px" >';
        }
        $this->assign ( $list_data );

        // add param
        $get_param = array('type'=> $map ['type'], 'object_id'=>i('object_id'));
        $this->assign('get_param',$get_param);

        // display
        $templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
        $this->display ( $templateFile );
    }

    public function listsAdmin($ajaxReturn = true, $map = null)
    {
        $result =  parent::listsAdmin(false, $map);
        foreach ( $result ['data'] as &$vo ) {
            $vo [0] = '<img src="' . get_cover_url ( $vo [0] ) . '" width="50px" >';
        }
        $this->ajaxReturn($result);
    }


    function add(){
        $this->assign('type',i('get.type'));
        $this->assign('object_id',i('get.object_id'));
        $_POST['type'] = i('get.type');
        $_POST['object_id'] = i('get.object_id');
        parent::add();
    }
}
