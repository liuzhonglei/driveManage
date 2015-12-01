<?php

namespace Addons\Message\Controller;

/**
 * 学员留言板
 * Class QuestionController
 * @package Addons\Messaget\Controller
 */
class MessageController extends MessageBaseController
{
    /**
     * 初始化
     */
    function _initialize()
    {
        $this->model = $this->getModel('student_question');
        parent::_initialize();
    }

    /**
     * 增加
     */
    public function add()
    {
        $_POST['openid'] = get_openid();
        $_POST['token'] = get_token();
        $_POST['time'] = date('Y-m-d H:i:s');
        parent::add();
    }

    /**
     * 查询列表
     */
    public function lists()
    {
        // get the data

        $list_data = $this->_get_model_list($this->model);
        $this->assign($list_data);


        //设置显示控件
        $this->assign('add_button', '0');

        // display
        $this->display();
    }

    public function _search_map($model, $fields)
    {
        $map = parent::_search_map($model, $fields);
        if (is_array($map)) {
            $where_str = "";

            foreach ($map as $mapMame => $mapValue) {
                if (!empty($where_str)) {
                    $where_str .= " and ";
                }
                $where_str .= $mapMame . ' = "' . $mapValue . '" ';
            }
            $map = $where_str;
        }

        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $showAnswer = $db_config['show_no_answer_question'];
        if (!$showAnswer) {
            $map .= " and answer is not null";
        }

        // 返回
        return $map;
    }


}
