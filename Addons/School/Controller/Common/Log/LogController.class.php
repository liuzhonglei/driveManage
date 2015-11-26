<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/11
 * Time: 14:30
 */
namespace Addons\School\Controller\Common\Log;
use Addons\School\Controller\BaseController;

/**
 * 日志控制器
 * Class LogController
 * @package Addons\School\Common\Log
 */
class LogController extends BaseController
{
    /**
     * 初始化
     */
    function _initialize() {
        $this->model = $this->getModel ( 'school_log' );

        parent::_initialize ();
    }

    /**
     * 记录日志
     * @param $text
     * @param $majorType
     * @param $majorId
     * @param string $level
     */
    public function  writeLog($text, $majorType, $majorId, $level = LogLevel::NORMAL)
    {
        $data = array(text=>$text,major_type=>$majorType, major_id=>$majorId,level=>$level,time=>time(),token=>get_token());
        M('school_log')->add($data);
    }

    /**
     * 查询日志
     * @param null $majorType
     * @param null $majorId
     * @param $level
     * @return mixed
     */
    public function queryLog( $majorType = null, $majorId = null, $level = LogLevel::NORMALl)
    {
        $map = array(majorType=>$majorType, majorId=>$majorId,level=>$level);
        return M('school_log')->where($map)->order('time desc')->select();
    }

    // 通用插件的列表模型
    public function lists($model = null, $page = 0) {
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        $this->assign('check_all',0);
        parent::common_lists ( 'school_log', $page, 'lists' );
    }

    /**
     * 查询日志
     * @param null $model
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = 'time desc')
    {
        return parent::_get_model_list($model, $page, $order);
    }
}
