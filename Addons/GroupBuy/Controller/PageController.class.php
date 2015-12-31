<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/12/20
 * Time: 11:30
 */

namespace Addons\GroupBuy\Controller;

use Addons\EO2OPayment\Controller\EO2OPaymentController;

/**
 * 团购报名活动界面
 * Class PageController
 * @package Addons\GroupBuy\Controller
 */
class PageController extends BaseController
{

    /**
     * 显示当前页面
     */
    function  show()
    {
        if (empty(M('follow')->where(array("token" => get_token(), "openid" => get_openid()))->find())) {
            $requestUrl  = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            redirect(createWeChat()->getOauthRedirect($requestUrl));
        }

        // 增加粉丝数据
        $code = i('code');
        if (!empty($code)) {
            D('Common/Follow')->updateOauthFollow();
        }


        // 取得本人数据
        $myInfo = $this->getMyInfo();
        $this->assign('myInfo', $this->getMyInfo());

        // 取得团购信息
        if (!empty($myInfo["party"])) {
            $groupBuyId = $myInfo["party"]['groupbuy_info_id'];
        } else {
            $groupBuyId = $_REQUEST['groupbuyid'];
        }


        $groupbuyInfo = R('Addons://GroupBuy/GroupBuy/getGroupbuyInfo', array($groupBuyId));
        $groupbuyInfo["partyLength"] = count($groupbuyInfo["partyList"]);
        if (empty($groupBuyId)) {
            $groupbuyInfo['status'] = 0;
        }
        $this->assign('groupbuyInfo', $groupbuyInfo);

        // 设置传入信息
        $this->assign($_GET);

        // 设置刷新路径
//        $this->assign("shareUrl", $this->getShareUrl($groupbuyInfo));

        // 取得课程信息
        $this->assign("courseList", $this->getCourseList());
        $this->assign('schoolInfo', $this->getSchoolInfo());

        // show page
        $this->display(T(MOBILE_PATH . "activityGroupon"));
    }


    /**
     * 参与团购
     */
    function join()
    {
        // 保存学员信息
        $_POST['openid'] = get_openid();

        // 取得团购类型信息
        $groupBuyId = $_REQUEST['groupBuyId'];

        // 已存在团购
        if (!empty($groupBuyId)) {
            $groupBuyInfo = R('Addons://GroupBuy/GroupBuy/getGroupbuyInfo', array($groupBuyId));
            if (empty($groupBuyInfo)) {
                $this->error("找不到对应的团购信息!");
            }
            if ($groupBuyInfo["status"] == 2) {
                $this->error("该团购已结束!");
            }
        } // 未存在团购
        else {
            // 创建团购信息
            $type = M('groupbuy_type')->where(array("token" => get_token()))->find();
            if (empty($type)) {
                $this->error("找不到对应的团购类型!");
            }
            $_POST['type_id'] = $type["id"];
            $_POST['time'] = date("Y-m-d");
            $groupBuyId = $this->saveModel("groupbuy_info")['id'];
        }

        // 创建参与人信息
        $_POST['groupbuy_info_id'] = $groupBuyId;
        $_POST['time'] = date("Y-m-d");
        $this->ajaxReturn($this->saveModel("groupbuy_party"));
    }

    /**
     * 进行付款
     */
    public
    function  payMoney()
    {
        $type = M('groupbuy_type')->where(array("token" => get_token()))->find();
        if ($type && $type["payitem_id"]) {
            $payItme = M("school_payitem")->where("id=" . $type['payitem_id'] . " and token = '" . get_token() . "'")->find();
            $_POST["payitem_id"] = $payItme["id"];
            $_POST["paytype"] = $payItme["type"];
            $_POST["total"] = $payItme["fee"];
            R('Addons://EO2OPayment/EO2OPayment/paymentData');
        } else {
            $this->ajaxReturn(array("status" => 0, "info" => "没有配置对应的支付项目!"));
        }
    }


    /**
     * 取得当前用户数据
     * @return array|mixed
     */
    private
    function getMyInfo()
    {
        // 取的学员信息
        $info = $this->getStudentInfo();
        if (empty($info)) {
            return array();
        }

        // 取得报名项目,以及费用
        if ($info['course_id']) {
            $info['course'] = $this->getCourseList($info['course_id'])[0];
        }

        // 取得团购信息
        $party = M("groupbuy_party")->where(array("token" => get_token(), "openid" => get_openid()))->find();
        $info['party'] = $party;

        // 取得支付数据
        $eO2OPaymentController = new EO2OPaymentController;
        $payRecord = $eO2OPaymentController->moneyLog()[0];
        $info['payRecord'] = $payRecord;

        // 返回
        return $info;
    }


    /**
     * 取得课程信息
     * @return mixed
     */
    private
    function getCourseList($courseId = null)
    {
        $params = array("token" => get_token());
        if ($courseId) {
            $params["id"] = $courseId;
        }
        $info = M('school_course')->where($params)->select();
        return $info;
    }
}