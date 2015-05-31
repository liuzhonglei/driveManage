<?php

namespace Addons\Student\Controller;


class QuestionController extends StudentBaseController{

    function _initialize() {
        $this->model = $this->getModel ( 'student_question' );
        parent::_initialize ();
    }
    public function add(){
        $_POST['open_id'] =  get_openid();
        $_POST['token'] =  get_token();
        $_POST['time'] =  date('Y-m-d H:i:s');
        parent::add();

    }

    // 通用插件的列表模型
    public function lists() {
        // 使用提示

        $map ['token'] = get_token ();
        session ( 'common_condition', $map );

        // condition
        $condition['token'] = get_token ();

        // get the data
        $list_data = $this->_get_model_list($this->model);
        $list_data = $this->replaceWeixin($list_data, 'open_id');
        $this->assign($list_data);

        //设置显示控件
        $this->assign('add_button','0');

        // display
        $this->display ();
    }
}
