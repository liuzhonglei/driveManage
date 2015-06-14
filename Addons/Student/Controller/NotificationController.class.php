<?php

namespace Addons\Student\Controller;
use Addons\Abcinfosend\Api\Wechat;

require_once ONETHINK_ADDON_PATH.'/Third/Controller/simple_html_dom.php';

class NotificationController extends StudentBaseController{
    const TIPS = '考试通知正常流程为：未发送->已发成功->学员读取。<br>由于"学员读取"有延时，请重复点击"同步"来同步通知结果，一般有2-3秒延时。<br>发送失败，请重新选择并点击"通知"。由于某些原因状态无法成功通知,请电话通知。';

    function _initialize() {
        $this->model = $this->getModel ( 'student_notification' );
        parent::_initialize ();

        // 子导航
        $action = strtolower(_ACTION);
        $res ['title'] = '通知';
        $res ['url'] = addons_url('Student://Notification/lists');
        $res ['class'] = ($action=='lists' || $action=='sync' || $action== 'notifcation')? 'cur':'';
        $nav [] = $res;

        // 子导航
        $action = strtolower(_ACTION);
        $res ['title'] = '配置';
        $res ['url'] = addons_url('Student://Notification/config');
        $res ['class'] = ($action=='config')? 'cur':'';
        $nav [] = $res;

        // assign
        $this->assign('sub_nav', $nav);
    }

    public function lists(){
        // tips
        $this->assign ( 'normal_tips', self::TIPS );
        $this->assign('sync_date',date('Y-m-d'));
        parent::lists();
    }


    public function sync(){
        $sync_date = i('sync_date');
        $sync_course = i('sync_course');
        $db_config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
        $exam_address = $db_config['notification_address_km'.$sync_course];

        // get the data
        $this->syncExamNotification($sync_date,$sync_course);

        // return the data
        $list_data = $this->_get_model_list($this->model,null,'noti_result');
        $this->assign($list_data);

        //设置显示控件
        $this->assign('add_button','0');
        $this->assign('del_button','0');

        $this->assign('search_button','0');
        $this->assign('sync_date',$sync_date);
        $this->assign('sync_course',$sync_course);
        $this->assign('exam_address',$exam_address);

        // display
        $this->assign ( 'normal_tips', self::TIPS );
        $this->display ('lists');
    }

    /**
     * notifaction the user eaxm
     */
    public function notifcation(){
//        $sync_date = i('sync_date');
//        $sync_course = i('sync_course');
        $exam_time = i('exam_time');
        $exam_address = i('exam_address');
        $token = get_token();
        $Notification = M($this->model['name']);
        $tempId = 'XTl_diLQIcO4E8xxJG9hGkSGa91-UvHnMegaR8EZH9s'; // todo


        // get the id
        $ids =i('ids');
        ! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

        // get the company
        $Model = M('school');
        $map = array('token' => $token);
        $school_info = $Model->where($map)->find();

        // get the wechat data
        $mapinfo['token'] = $token;
        $info = M ( 'member_public' )->where ( $mapinfo )->find ();
        $this->assign ( 'info', $info );
        $options = array(
            'token'=>'weiphp', //填写你设定的key,本系统中固定weiphp
            'appid'=>$info['appid'],
            'appsecret'=>$info['secret'],
        );
        $weObj = new Wechat($options);

        // get the send data
        $map='sync_date = "'.i('sync_date').'" and sync_course= "'.i('sync_course').'" and token = "'.get_token().'" and id in ('.$ids.')';
        $dataList =  $Notification->where($map)->select();
        foreach($dataList as &$data){
            // set the status
            if($data['noti_result']=='1' || $data['noti_result']=='2'){
                continue;
            }

            // get the student data
            $map = 'name="'.$data['name'].'"and card_id="'.$data['card_id'].'" and token = "'.get_token().'"';
            $studentInfo = M('student')->where($map)->find();
            $data['time'] = $exam_time;
            if(empty($studentInfo)){
                // change the status
                $data['noti_result'] = '-1';
                $data['remark'] = '查找不到对应学员!';
            }else if(empty($studentInfo['openid'])){
                // send notification
                $data['noti_result'] = '-1';
                $data['remark'] = '学员没有绑定对应微信号!';
            }else{
                $tempData = array(
                    'first'=> array('value'=>$studentInfo['name'].'您好，您的考试已安排成功！', 'color'=>'#173177'),
                    'keyword1'=> array('value'=>'科目'.$data['sync_course'], 'color'=>"#173177"),
                    'keyword2'=> array('value'=>$data['date'].' '.$data['time'], 'color'=>'#173177'),
                    'keyword3'=> array('value'=>$exam_address, 'color'=>'#173177'),
                    'keyword4'=> array('value'=>$school_info['phone'], 'color'=>'#173177'),
                    'remark'=> array('value'=>"请提前1个小时到达考场，逾期以挂科处理。", 'color'=>'#173177'),
                );
                // 记录日志
                addWeixinLog ( $tempData,'$tempData' );
                if($return = $weObj->sendTempMes($studentInfo['openid'],$tempId,$tempData)){
                    if($return['errcode']  == '0'){
                        $data['noti_result'] = '1';
                        $data['msgid'] = $return['msgid'];
                        $data['remark'] = '发送通知成功!';
                        addWeixinLog ( $return,'$return' );
                    }else{
                        $data['noti_result'] = '-1';
                        $data['remark'] = '发送通知失败,错误代码['.$return[errcode].']错误信息['.$return[errmsg].']!';
                    }
                }else{
                    $data['noti_result'] = '-1';
                    $data['remark'] = '发送通知失败,错误代码['.$weObj->errCode.']错误信息['.$weObj->errMsg.']!';
                }
            }

            $Notification->data($data)->save();
        }

        // return the data
        $list_data = $this->_get_model_list($this->model,null,'noti_result');
        $this->assign($list_data);

        //设置显示控件
        $this->assign('add_button','0');
        $this->assign('del_button','0');
        $this->assign ( 'normal_tips', self::TIPS );
        $this->assign('search_button','0');
        $this->assign($_GET);

        // display
        $this->display ('lists');
    }


    // get the exam notification info form html
   private function syncExamNotification($sync_date=null, $km=null){

       require_once ONETHINK_ADDON_PATH.'/Third/Controller/simple_html_dom.php';

       $token = get_token();

       if(empty($sync_date)){
           $this->error('请填写同步日期');
       }

       if(empty($km)){
           $this->error('请填写同步科目');
       }

       $db_config = D ( 'Common/AddonConfig' )->get ( _ADDONS );

       $chkkey = $db_config['notification_chkkey'];
       $rand = $db_config['notification_rand'];
       $pxdid = $db_config['notification_pxdid'];
       if(empty($rand)){
           $this->error('请到配置界面配置考试通知rand');
       }
       if(empty($chkkey)){
           $this->error('请到配置界面配置考试通知chkkey');
       }
       if(empty($pxdid)){
           $this->error('请到配置界面配置考试通知pxdid');
       }

        // get the html
        $ulr = 'http://110.86.26.150:9098/showyyresult.asp?ksrq='.$sync_date.'&km='.$km.'&chkkey='.$chkkey.'&rand='.$rand.'&pxdid='.$pxdid;
        $html = new \simple_html_dom();
        $html->load_file($ulr);

        // get the data
        $trList = $html->find('TBODY', 2);
       if(empty($trList)){
           return null;
       }
       $trList =  $trList->find('tr');

       $Notif = M($this->model['name']);
       $addData = array();
       for ($i = 1; $i < sizeof($trList) - 1; $i++){
           $name = iconv('GB2312','UTF-8', $trList[$i]->children(1)->plaintext);
           $exam_date = $trList[$i]->children(2)->plaintext;
           $card_id = $trList[$i]->children(3)->plaintext;
           $result = iconv('GB2312','UTF-8',$trList[$i]->children(4)->plaintext);

           if (!empty($name) && !empty($exam_date) && !empty($card_id) && !empty($result)) {
               $data = array('name' => $name, 'sync_date' => $sync_date,'sync_course'=>$km, 'card_id' => $card_id,'token'=>$token);
               $obj_info = $Notif->where($data)->find();
              if(empty($obj_info)){
                    $data['date'] = $exam_date;
                    $data['result'] = $result;
                    $data['token'] = $token;
                    $data['noti_result'] = '0';
                    $addData[] = $data;
               }
           }
       }

       $Notif->addAll($addData);

    }


    // add the search map
    public function _search_map($model, $fields) {
        $map='sync_date = "'.i('sync_date').'" and sync_course= "'.i('sync_course').'" and token = "'.get_token().'"';
        return $map;
    }
}
