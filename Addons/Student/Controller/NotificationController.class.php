<?php

namespace Addons\Student\Controller;

use Addons\Abcinfosend\Api\Wechat;

require_once ONETHINK_ADDON_PATH . '/Third/Controller/simple_html_dom.php';

class NotificationController extends StudentBaseController
{
    const TIPS = '考试通知正常流程为：未发送->已发成功->学员已通知。<br>由于"学员读取"有延时，请重复点击"同步"来同步通知结果，一般有2-3秒延时。<br>发送失败，请重新选择并点击"通知"。由于某些原因状态无法成功通知,请电话通知。';

    function _initialize()
    {
        $this->model = $this->getModel('student_notification');
        parent::_initialize();

        // 子导航
        $action = strtolower(_ACTION);
        $controller = strtolower(_CONTROLLER);
        $res ['title'] = '通知';
        $res ['url'] = addons_url('Student://Notification/lists');
        if ($controller == 'notification' && ($action == 'lists' || $action == 'sync' || $action == 'notifcation' || $action == 'noticonfirm')) {
            $res ['class'] = 'cur';
        } else {
            $res ['class'] = '';
        }
        $nav [] = $res;

        $action = strtolower(_ACTION);
        $res ['title'] = '帐号配置';
        $res ['url'] = addons_url('Student://NotificationAccount/lists');
        $res ['class'] = ($action == 'lists' || $action == 'sync' || $action == 'notifcation') ? 'cur' : '';
        if ($controller == 'notificationAccount') {
            $res ['class'] = 'cur';
        } else {
            $res ['class'] = '';
        }
        $nav [] = $res;

        $action = strtolower(_ACTION);
        $res ['title'] = '配置';
        $res ['url'] = addons_url('Student://Notification/config');
        $res ['class'] = ($action == 'config') ? 'cur' : '';
        $nav [] = $res;


        // assign
        $this->assign('sub_nav', $nav);
    }

    /**
     * show the list
     */
    public function lists()
    {
        $_GET['chkkey'] = $this->geChkkey();

        // tips
        $this->assign('normal_tips', self::TIPS);
        $this->assign('sync_date', date('Y-m-d'));
        $this->assign('accounts', M('school_noti_account')->where('token="' . get_token() . '"')->select());
        parent::lists();
    }

    /**
     * get the current chkkey
     */
    private function geChkkey()
    {
        $sycn_account = $_GET['sync_account'];
        $accountInfo = M('school_noti_account')->where('id=' . $sycn_account . ' and token="' . get_token() . '"')->find();
        return $accountInfo['chkkey'];
    }


    public function sync()
    {

        $sync_date = i('sync_date');
        $sync_course = i('sync_course');
        $sync_account = i('sync_account');

        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $exam_address = $db_config['notification_address_km' . $sync_course];

        $_GET['chkkey'] = $this->geChkkey();
        $_GET['sync_course'] = $sync_course;
        $_GET['exam_address'] = $exam_address;

        // get the data
        $this->syncExamNotification($sync_date, $sync_course, $sync_account);

        // return the data
        $list_data = $this->_get_model_list($this->model, null, 'noti_result');
        $this->assign($list_data);

        //设置显示控件
        $this->assign('add_button', '0');
        $this->assign('del_button', '0');

        $this->assign('search_button', '0');
        $this->assign('sync_date', $sync_date);
        $this->assign('accounts', M('school_noti_account')->where('token="' . get_token() . '"')->select());
        $this->assign('noti_num', sizeof($list_data['list_data']));
        $this->assign('sync_course', $sync_course);
        $this->assign('sync_account', $sync_account);
        $this->assign('exam_address', $exam_address);

        // display
        $this->assign('normal_tips', self::TIPS);
        $this->display('lists');
    }

    /**
     * confirm the notification
     */
    public function notiConfirm()
    {
        // param
        $token = get_token();
        $id = i('id');
        $Notification = M($this->model['name']);
        $_GET['chkkey'] = $this->geChkkey();

        $noti_data = $Notification->where('token = "' . $token . '" and id=' . $id)->find();
        if ($noti_data['noti_result'] != '2') {
            $noti_data['noti_result'] = '2';
            $noti_data['remark'] = '管理员已人工进行通知。';
            $Notification->data($noti_data)->save();
        }

        $list_data = $this->_get_model_list($this->model, null, 'noti_result');
        $this->assign($list_data);

        //设置显示控件
        $this->assign('add_button', '0');
        $this->assign('del_button', '0');
        $this->assign('normal_tips', self::TIPS);
        $this->assign('search_button', '0');
        $this->assign('noti_num', sizeof($list_data['list_data']));
        $this->assign('accounts', M('school_noti_account')->select());
        $this->assign($_GET);

        // display
        $this->display('lists');
    }


    /**
     * 对选中学员进行通知操作
     * notifaction the user eaxm
     */
    public function notifcation()
    {
        $_GET['chkkey'] = $this->geChkkey();
//        $exam_time = i('exam_time');
        $exam_address = i('exam_address');
        $token = get_token();
        $db_config = D('Common/AddonConfig')->get(_ADDONS);
        $tempId = $db_config['temp_id'];
        !empty ($tempId) || $this->error('请配置消息模板ID!');


        // get the id
        $ids = i('ids');
        !empty ($ids) || $this->error('请选择要操作的数据!');

        // get the company
        $Model = M('school');
        $map = array('token' => $token);
        $school_info = $Model->where($map)->find();

        // get the wechat data
        $mapinfo['token'] = $token;
        $info = M('member_public')->where($mapinfo)->find();
        $this->assign('info', $info);
        $options = array(
            'token' => 'weiphp', //填写你设定的key,本系统中固定weiphp
            'appid' => $info['appid'],
            'appsecret' => $info['secret'],
        );
        $weObj = new Wechat($options);

        // get the send data
        $map = ' and token = "' . get_token() . '" and id in (' . $ids . ')';
        $dataList = M('student')->where($map)->select();
        foreach ($dataList as &$data) {
            $tempData = array(
                'first' => array('value' => $data['name'] . '您好，您的考试已安排成功！', 'color' => '#173177'),
                'keyword1' => array('value' => '科目' . $data['status'], 'color' => "#173177"),
                'keyword2' => array('value' => $data['date'] . ' ' . $data['time'], 'color' => '#173177'),
                'keyword3' => array('value' => $exam_address, 'color' => '#173177'),
                'keyword4' => array('value' => $school_info['phone'], 'color' => '#173177'),
                'remark' => array('value' => "请提前1个小时到达考场，逾期以挂科处理。", 'color' => '#173177'),
            );
            // 记录日志
            if ($return = $weObj->sendTempMes($data['openid'], $tempId, $tempData)) {
                if ($return['errcode'] == '0') {
                    $data['exam_notify'.$data["status"]] = '1';
                    // 保存
                    M("student")->data($data)->save();
                    R("Addons://Controller/Common/Log/Log/writeLog",array("学员进行考试通知，科目[" . $data["status"] . "]发送通知中!", LogMajorType::STUDENT, $data["id"]));
                }else{
                    $data['exam_notify'.$data["status"]] = '0';
                    // 保存
                    M("student")->data($data)->save();
                    R("Addons://Controller/Common/Log/Log/writeLog",array("学员进行考试通知，科目[" . $data["status"] . "]发送通知失败!", LogMajorType::STUDENT, $data["id"]));
                }
            }
        }
    }


    // get the exam notification info form html
    private function syncExamNotification($sync_date = null, $km = null, $sync_account = null)
    {

        require_once ONETHINK_ADDON_PATH . '/Third/Controller/simple_html_dom.php';

        $token = get_token();

        if (empty($sync_date)) {
            $this->error('请填写同步日期');
        }

        if (empty($km)) {
            $this->error('请填写同步科目');
        }

        $accountInfo = M('school_noti_account')->where('id=' . $sync_account . ' and token="' . $token . '"')->find();

        $chkkey = $accountInfo['chkkey'];
        $rand = $accountInfo['rand'];
        $pxdid = $accountInfo['pxdid'];
        if (empty($rand)) {
            $this->error('请到配置界面配置考试通知rand');
        }
        if (empty($chkkey)) {
            $this->error('请到配置界面配置考试通知chkkey');
        }
        if (empty($pxdid)) {
            $this->error('请到配置界面配置考试通知pxdid');
        }

        // get the html
        $ulr = 'http://110.86.26.150:9098/showyyresult.asp?ksrq=' . $sync_date . '&km=' . $km . '&chkkey=' . $chkkey . '&rand=' . $rand . '&pxdid=' . $pxdid;
        $html = new \simple_html_dom();
        $html->load_file($ulr);

        // get the data
        $trList = $html->find('TBODY', 2);
        if (empty($trList)) {
            return null;
        }
        $trList = $trList->find('tr');

        $Notif = M($this->model['name']);
        $addData = array();
        for ($i = 1; $i < sizeof($trList) - 1; $i++) {
            $name = iconv('GB2312', 'UTF-8', $trList[$i]->children(1)->plaintext);
            $exam_date = $trList[$i]->children(2)->plaintext;
            $card_id = $trList[$i]->children(3)->plaintext;
            $result = iconv('GB2312', 'UTF-8', $trList[$i]->children(4)->plaintext);

            if (!empty($name) && !empty($exam_date) && !empty($card_id) && !empty($result)) {
                $data = array('name' => $name, 'sync_date' => $sync_date, 'sync_course' => $km, 'card_id' => $card_id, 'token' => $token);
                $obj_info = $Notif->where($data)->find();
                if (empty($obj_info)) {
                    $data['date'] = $exam_date;
                    $data['result'] = $result;
                    $data['token'] = $token;
                    $data['chkkey'] = $chkkey;
                    $data['noti_result'] = '0';
                    $addData[] = $data;
                }
            }
        }

        $Notif->addAll($addData);
    }


    // add the search map
    public function _search_map($model, $fields)
    {
        $map = 'sync_date = "' . i('sync_date') . '" and sync_course= "' . i('sync_course') . '" and chkkey= "' . i('chkkey') . '"  and token = "' . get_token() . '"';
        return $map;
    }
}
