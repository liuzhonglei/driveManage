<?php

namespace Addons\Student\Controller;

class AppraiseController extends StudentBaseController{
    function _initialize() {
        $this->model = $this->getModel ( 'student_apprise' );
        parent::_initialize ();
    }

    // 通用插件的列表模型
    public function lists() {
        // check
        $map ['token'] = get_token ();
        session ( 'common_condition', $map );

        // condition
        $_POST['token'] = get_token ();

        // get data
        $list_data = $this->_get_model_list ( $this->model );

        // teacher data
        $list_data =  $this->replaceTeacerId($list_data,'id_teacher');
        $list_data =  $this->replaceStudentId($list_data,'id_student');

        // configure data
        $this->assign ( $list_data );

        //设置显示控件
        $this->assign('add_button','0');

        // display
        $this->display ( "lists" );
    }
}
