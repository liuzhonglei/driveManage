<?php

namespace Addons\CutPrice\Controller;

/**
 * Class CutPriceCountController
 * @package Addons\CutPrice\Controller
 */
class PageController extends BaseController
{
    /**
     * init
     */
    function _initialize()
    {
        $this->model = $this->getModel('cut_price');
        parent::_initialize();
    }

    /**
     * show the main page
     */
    function show()
    {
        // param
        $info = $this->getActivityInfo();
        $cutInfo = $this->getMyCutRecord();
        $cutResult = $this->getCutResult();

        if (!empty($info) && !empty($cutResult)) {
            $courseInfo = M('school_course')->where("id = '" . $info["course_id"] . "'")->find();
            $cutResult['course_name'] = $courseInfo["name"];
            $cutResult['total_fee'] = $courseInfo["sign_charge"];
            $cutResult['current_fee'] = $courseInfo["sign_charge"] - $cutResult["fee"];
        }

        $this->assign('schoolInfo', $this->getSchoolInfo());
        $this->assign("info", $info);
        $this->assign("cutInfo", $cutInfo);
        $this->assign("cutResult", $cutResult);
        $this->assign("cutList", $this->getCutList());
        $this->assign("courseList", $this->getCourseList());

        // get the conf info
        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $activityInfo = $db_config;
        $beginTime = strtotime(trim($db_config["begin_date"]) . " 00:00:01");
        $endTime = strtotime(trim($db_config["end_date"]) . " 23:59:59");
        $max_sum_money = intval($db_config['max_sum_money']);
        $max_number_people = intval($db_config['max_number_people']);
        $currentTime = time();

        // judge
        if ($currentTime < $beginTime || ($currentTime > $endTime && $cutResult['fee'] == 0)) {
            $activityInfo['status'] = 0;
        } else if ($this->isMyActivity() && ($currentTime > $endTime || $cutResult['fee'] >= $max_sum_money || $cutResult['count'] >= $max_number_people)) {
            $activityInfo['status'] = 5;
        }else if(!$this->isMyActivity() &&  $currentTime > $endTime){
            $activityInfo['status'] = 6;
        }
         // if the activity is not end
        else {

            // remain time
            $timeRemain = $endTime - $currentTime;
            $activityInfo['timeRemain'] = $timeRemain;

            // status
            if ($this->isMyActivity()) {
                if (empty($info)) {
                    $activityInfo['status'] = 1;
                } else {
                    $activityInfo['status'] = 2;
                }
            } else {
                if(empty($info)) {
                    $activityInfo['status'] = 1;
                }else if (empty($cutInfo)) {
                    $activityInfo['status'] = 3;
                } else {
                    $activityInfo['status'] = 4;
                }
            }
        }

        // set info
        $this->assign($_GET);
        $this->assign('activityInfo', $activityInfo);

        // show page
        $this->display(T(MOBILE_PATH . "activityCutPrice"));
    }

    /**
     * register the info and show the page
     */
    function register()
    {
        $_POST['status'] = '-1';
        $_POST['intro_source'] = '0';
        $_POST['time_sign'] = date("Y-m-d");

        $_POST['openid'] = get_openid();
        $this->ajaxReturn($this->saveModel("student"));
    }

    /**
     * cut the price and show the page
     */
    function cutPrice()
    {
        // get
        $activityOpenid = $_GET["activityopenid"];
        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $fee = $db_config['money_options'][array_rand($db_config['money_options'])];

//        $userName = get_nickname(get_openid());


        // 查找是否已存在
        $param = array("token" => get_token(), "friend_openid" => get_openid(), 'openid' => $activityOpenid);
        if (empty(M('cut_price')->where($param)->find())) {

            // 不能超过最大值
            $db_config = D('Common/AddonConfig')->get(_ADDONS);
            $max_sum_money = intval($db_config['max_sum_money']);
            $max_number_people = intval($db_config['max_number_people']);

            $cutResult = $this->getCutResult();
            if ($cutResult['count'] >= $max_number_people || $cutResult['fee'] >= $max_sum_money) {
                $this->ajaxReturn(array("status" => 0, "info" => "活动已结束！"));
            }
            if ($fee > $max_sum_money - $cutResult['fee']) {
                $fee = $max_sum_money - $cutResult['fee'];
            }

            // set
            $_POST['openid'] = $activityOpenid;
            $_POST['friend_openid'] = get_openid();
//            $_POST['friend_name'] = $userName;
            $_POST['token'] = get_token();
            $_POST['fee'] = $fee;
            $_POST['time'] = date("Y-m-d H:i:s");

            // save
            $this->saveModel();
        }

        //return
        redirect(U("show", array("activityOpenid" => $activityOpenid)));
    }

    /**
     * get the teacher course
     * @return mixed
     */
    function getCourseList()
    {
        $params = array("token" => get_token());
        $info = M('school_course')->where($params)->select();
        return $info;
    }

    /**
     * get my student info
     * @return info student info
     */
    function getActivityInfo()
    {
        $openid = $_GET["activityopenid"];
        $openid || $openid = get_openid();
        $params = array("token" => get_token(), "openid" => $openid);
        $info = M('student')->where($params)->find();
        if (!empty($info)) {
            $info["photo_url"] = get_cover($info["photo"]);
            $info["photo_url"] || $info["photo_url"] = MOBILE_PUBLIC_PATH . "/img/nonePic.png";
        }

        return $info;
    }

    /**
     * is my activity or others
     * @return bool
     */
    function isMyActivity()
    {
        $activityOpenid = $_GET["activityopenid"];
        $openid = get_openid();
        if (empty($activityOpenid) || $activityOpenid == $openid) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the cut activity result
     * @return result
     */
    function getCutResult()
    {
        $openid = $_GET["activityopenid"];
        $openid || $openid = get_openid();

        $params = array("token" => get_token(), "openid" => $openid);
        $result = M('cut_price_count')->where($params)->find();
        if ($result) {
            return $result;
        } else {
            return array("count" => 0, "fee" => 0);
        }
    }

    /**
     * get my cut record
     * @return mixed
     */
    function getMyCutRecord()
    {
        $activityOpenid = i("activityopenid");
        if (!empty($activityOpenid)) {
            $params = array("token" => get_token(), "openid" => $activityOpenid, "friend_openid" => get_openid());
            $result = M('cut_price')->where($params)->find();
            return $result;
        } else {
            return null;
        }
    }

    /**
     * get my cut list
     * @return mixed
     */
    function getCutList()
    {
        $openid = $_GET["activityopenid"];
        $openid || $openid = get_openid();

        if (!empty($openid)) {
            $params = array("token" => get_token(), "openid" => $openid);
            $result = M('cut_price')->where($params)->select();
            return $result;
        } else {
            return array();
        }
    }
}
