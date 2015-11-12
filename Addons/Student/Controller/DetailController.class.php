<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/11
 * Time: 15:56
 */

namespace Addons\Student\Controller;
use Addons\School\Controller\Common\Log\LogController;
use Addons\EO2OPayment\Controller\EO2OPaymentController;
use Addons\School\Controller\Common\Log\LogMajorType;

class DetailController extends StudentBaseController{
    /**
     * init
     */
    function _initialize()
    {

        parent::_initialize();
        $action = strtolower(_ACTION);

        // 子导航
        $param = array('student_id' => $_REQUEST['student_id']);

        $res ['title'] = '日志';
        $res ['url'] = addons_url('Student://Detail/log',$param);
        $res ['class'] = ($action == 'log') ? 'cur' : '';
        $nav [] = $res;

        $res ['title'] = '划款信息';
        $res ['url'] = addons_url('Student://Detail/moneyLog',$param);
        $res ['class'] = ($action == 'moneylog') ? 'cur' : '';
        $nav [] = $res;

        // assign
        $this->assign('sub_nav', $nav);
    }

    /**
     * 学员的日志
     */
    function log(){
        $_REQUEST['major_type'] = LogMajorType::STUDENT;
        $_REQUEST['major_id'] = $_REQUEST['student_id'];
        $logController = new LogController();
        $logController->lists();
    }

    /**
     * 学员的划款流水
     */
    function  moneyLog(){
        $where = 'id  = ' .  $_REQUEST['student_id'] . '';
        $student = M("student")->where($where)->find();
        $_REQUEST['openid'] = $student['openid'];
        $nav = $this->get('nav');
        $subNav = $this->get('sub_nav');
        $paymentController = new EO2OPaymentController();
        $paymentController->lists($nav,$subNav);
    }
}