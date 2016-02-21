<?php

namespace Addons\EO2OPayment\Controller;

include_once "WxPayPubHelper.php";

class EO2OPaymentController extends EO2OBaseController
{
    // the pay type info
    private static $paytypeInfo = array('banner' => array('page' => 'Addons://EO2OPayment@EO2OPayment/Payment', 'body' => '教练锦旗', 'callback' => 'Student://Banner/add', 'attach' => '测试数据'));

    var $model;

    function _initialize()
    {
        $this->model = $this->getModel('eo2o_payment');
        $this->fields = array("openid");
        parent::_initialize();
    }

    // 通用插件的列表模型
    public function lists($nav = null, $subNav = null)
    {
        if (!empty($nav) || !empty($subNav)) {
            $this->assign('nav', $nav);
            $this->assign('sub_nav', $subNav);
        } else {
            $top_more_button = array(array("is_buttion" => "0", "title" => "EXL导出", "url" => U("downloadExcel")));
        }
        $this->assign('del_button', 0);
        $param = Array();
        if (!empty($_REQUEST['student_id'])) {
            $param['student_id'] = $_REQUEST['student_id'];
        }
        $this->assign('add_url', addons_url('EO2OPayment://EO2OPayment/add', $param));

        $this->assign('check_all', 0);
        $this->assign('top_more_button', $top_more_button);
        parent::lists();
    }


    /**
     * 通用插件的删除模型
     */
    public function del()
    {
        $info = M('eo2o_payment')->where('id=' . $_REQUEST['id'])->find();
        if ($info && $info['pay_channel'] != "human") {
            $this->error("只允许编辑人工数据");
        }
        parent::common_del($this->model);
    }

    /**
     * 增加
     */
    public function add($nav, $subNav)
    {
        if (IS_POST) {
            $_POST["result_code"] = "SUCCESS";
            $_POST["pay_channel"] = "human";
        } else {
            if (!empty($nav)) {
                $this->assign('nav', $nav);
            }
            if (!empty($subNav)) {
                $this->assign('sub_nav', $subNav);
            }
            $this->assign('student_id', $_REQUEST['student_id']);
        }

        //增加
        $this->model['template_add'] = "./Addons/EO2OPayment/View/default/EO2OPayment/add.html";
        parent::add();
    }

    /**
     * 增加划款事项
     * @param $fields
     * @return \Addons\School\Controller\Common\Controller\当前的字段
     */
    public function getFieldList($fields)
    {
        $fields = parent::getFieldList($fields);
        $param = array(token => get_token());
        if (!empty(i("in_or_out"))) {
            $param["in_or_out"] = i("in_or_out");
        }
        $fields = $this->setFiledExtra($fields, "payitem_id", 'school_payitem', 'name',$param );
        // 划款项目
        for ($i = 1; $i <= count($fields); $i++) {
            for ($j = 0; $j < count($fields[$i]); $j++) {
                $fieldInfo = $fields[$i][$j];
                if ($fieldInfo['name'] == "payitem_id") {
                    $extraData = parent::getFieldData('school_payitem', array('token' => get_token()), 'name', $fieldInfo['is_must'] == "1","in_or_out");
                    $fieldInfo ['extra'] = $extraData;
                    $fields[$i][$j] = $fieldInfo;
                }
            }
        }

        //其他
//        $fields = $this->setFiledExtra($fields, "student_id", 'student', 'name', array("token" => get_token(), "can_pay" => array('lt', 99)));
        $fields = $this->setFiledExtra($fields, "school_place_id", 'school_place', 'name', array("token" => get_token(), "can_pay" => "1"), true);

        // 设置事件默认值
        for ($i = 1; $i <= count($fields); $i++) {
            for ($j = 0; $j < count($fields[$i]); $j++) {
                $fieldInfo = $fields[$i][$j];
                if ($fieldInfo["name"] == "time_end") {
                    $fields[$i][$j]["value"] = date("Y-m-d h:i");
                }
            }
        }

        // 返回
        return $fields;
    }


    /**
     * 修改
     */
    public function edit()
    {
        if (IS_POST) {
        } else {
            $info = M('eo2o_payment')->where('id=' . $_REQUEST['id'])->find();
            if ($info && $info['pay_channel'] != "human") {
                $this->error("只允许编辑人工数据");
            }


            if (!empty($nav)) {
                $this->assign('nav', $nav);
            }
            if (!empty($subNav)) {
                $this->assign('sub_nav', $subNav);
            }
        }

        parent::edit();
    }


    /**
     * 需要修改
     * 获取模型列表数据
     */
    public function _get_model_list($model = null, $page = 0, $order = 'id desc', $map)
    {
        $list_data = parent::_get_model_list($model, $page, $order, $map);

        // if referrer is student set in_name
        foreach ($list_data['list_data'] as &$data) {
            if (!empty($data["student_id"])) {
                $info = M('student')->where('token = "' . get_token() . '" and id="' . $data["student_id"] . '"')->find();
                $data["student_name"] = $info["name"];
                $openid = $data["openid"];
                $openid || $openid = $info["openid"];
                if ($openid) {
                    $info = M('follow')->where('token = "' . get_token() . '" and openid="' . $openid . '"')->find();
                    $data["nickname"] = $info["nickname"];
                }
            } else if (!empty($data["openid"])) {
                $info = M('student')->where('token = "' . get_token() . '" and openid="' . $data["openid"] . '"')->find();
                $data["student_name"] = $info["name"];
                $data["student_id"] = $info["id"];
                $info = M('follow')->where('token = "' . get_token() . '" and openid="' . $data["openid"] . '"')->find();
                $data["nickname"] = $info["nickname"];
            }
        }
        return $list_data;
    }


    /**
     * start pay request
     */
    public function paymentPage()
    {
        $payType = i('paytype');
        $payPatameters = $this->getPayParameters();
        $this->assign("total", I("total"));
        $this->assign("desc", I("desc"));
        $this->assign("jsApiParameters", $payPatameters);
        $pageUrl = T(self::$paytypeInfo[$payType]['page']);
        $this->display($pageUrl);
    }

    /**
     * start pay request
     */
    public function paymentData()
    {
        $payPatameters = $this->getPayParameters();
        $this->ajaxReturn($payPatameters);
    }

    /**
     * 取得支付信息
     */
    function getPayParameters()
    {
        // param
        $appinfo = get_token_appinfo();
        $payType = i('paytype');
        $config = getAddonConfig('EO2OPayment');
        $wxconfig = array(
            'APPID' => $appinfo['appid'],
            'MCHID' => $config['MCHID'],
            'KEY' => $config['KEY'],
            'APPSECRET' => $appinfo['secret'],
            'SSLCERT_PATH' => $config['SSLCERT_PATH'],
            'SSLKEY_PATH' => $config['SSLKEY_PATH'],
            'NOTIFY_URL' => $config['NOTIFY_URL'],
            'CURL_TIMEOUT' => 30
        );

        // 请求预支付ID
        $unifiedOrder = new \UnifiedOrder_pub($wxconfig);
        $unifiedOrder->setParameter("openid", get_openid());
        $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型


        //订单号
        $timeStamp = time();
        $out_trade_no = $appinfo['appid'] . "$timeStamp";
        $unifiedOrder->setParameter("out_trade_no", "$out_trade_no");//商户订单号

        // attach
        switch ($payType) {
            case "banner":
                $attach = array("token" => get_token(), "fee" => $_REQUEST['total'], "teacher_id" => $_REQUEST['teacher_id'], "student_id" => $_REQUEST['student_id']);
                $unifiedOrder->setParameter("attach", json_encode($attach));//附加数据
                $unifiedOrder->setParameter("body", self::$paytypeInfo[$payType]['body']);//商品描述
                break;
            default:
                $payitemInfo = M('school_payitem')->where("id =" . i('payitem_id'))->find();
                $payType = $payitemInfo["type"];
                $unifiedOrder->setParameter("body", $payitemInfo["name"]);
        }

        //总金额
        $total = I("total");
        $total = $total * 100;
        $unifiedOrder->setParameter("total_fee", $total);

        // notify url
        $url = U('/addon/EO2OPayment/EO2OPayment/Notify', array("token" => get_token(), "openid" => get_openid()));
        $url = str_replace("?s=", "", $url);
        $unifiedOrder->setParameter("notify_url", $url);//默认通知地址

        // get the $prepay_id
        $prepay_id = $unifiedOrder->getPrepayId();
        if ($prepay_id['return_code'] == 'FAIL') {
            $this->error($prepay_id['return_msg']);
        }
        $transaction = array_merge($unifiedOrder->parameters, $unifiedOrder->result);
        $transaction['paytype'] = $payType;
        $transaction['token'] = get_token();
        $transaction['time_begin'] = time();

        $transaction["remark"] = i('remark');
        $transaction["payitem_id"] = i('payitem_id');
        $Model = M('eo2o_payment');
        $Model->add($transaction);
        if (!empty($Model->getError())) {
            $this->error($Model->getError());
        }

        if (!$prepay_id) {
            $this->error($unifiedOrder->result['return_msg']);
        }

        //使用jsapi接口,返回请求支付
        $jsApi = new \JsApi_pub($wxconfig);
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        return $jsApiParameters;
    }


    public function Notify()
    {
        $appinfo = get_token_appinfo();
        $config = getAddonConfig('EO2OPayment');
        $wxconfig = array(
            'APPID' => $appinfo['appid'],
            'MCHID' => $config['MCHID'],
            'KEY' => $config['KEY'],
            'APPSECRET' => $appinfo['secret'],
            'SSLCERT_PATH' => $config['SSLCERT_PATH'],
            'SSLKEY_PATH' => $config['SSLKEY_PATH'],
            'CURL_TIMEOUT' => 30
        );
        //使用通用通知接口
        $notify = new \Notify_pub($wxconfig);

        //存储微信的回调
        $xml = file_get_contents("php://input");
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;


        //update the transaction data
        $responseData = $notify->getData();
        $Model = M('eo2o_payment');
        $transaction = $Model->where('out_trade_no="' . $responseData['out_trade_no'] . '"')->find();

        // get the data
        $responseData['token'] = get_token();
        $responseData['time_end'] = time();
        if (!empty($transaction)) {
            $responseData["id"] = $transaction["id"];
            $Model->save($responseData);
        } else {
            $Model->add($responseData);
        }

        if ($notify->checkSign() == TRUE) {
            //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
            $data = array_merge(array("out_trade_no" => $responseData['out_trade_no'], "time" => date('Y-m-d H:i:s')), (array)json_decode($responseData['attach']));
            $data = http_build_query($data);

            $opts = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                        "Content-Length: " . strlen($data) . "\r\n",
                    'content' => $data
                )
            );
            $context = stream_context_create($opts);
            $url = addons_url(self::$paytypeInfo[$transaction['paytype']]['callback']);
            file_get_contents($url, false, $context);
        }
    }


    public function PayTo()
    {
        if (IS_POST) {
            $map['total'] = I('total');
            $map['desc'] = I('desc');
            $map['notify_url'] = urlencode('EO2OPayment://EO2OPayment/CallBackLog');//回调插件名称 可选参数
            $map['out_trade_no'] = get_token() . time();//可选参数，用户自己的订单编号
            $map['token'] = get_token();//可选参数，weiphp系统内跳转无需传入
            $map['openid'] = get_openid();//可选参数，weiphp系统内跳转无需传入
            $url = addons_url('EO2OPayment://EO2OPayment/Payment', $map);
            header('Location:' . $url);
        } else {
            $this->display();
        }

    }

    //https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7 参数参考
    public function CallBackLog()
    {
        $this->log(I('out_trade_no'));
        $this->log(I('result_code'));
    }

    /**
     * 取得微信号划款流水
     */
    function  moneyLog($openid = null, $studentId = null)
    {
        $map = array();
        if (!empty($openid)) {
            $map['openid'] = $openid;
        }
        if (!empty($studentId)) {
            $map['student_id'] = $studentId;
        }



        $map['openid'] || $map['openid'] = get_openid();
        $map['token'] = get_token();
        $map['result_code'] = "SUCCESS";
        return M("eo2o_payment")->where($map)->select();
    }

    /**
     * get the data by id
     * @param null $model
     * @param int $id
     */
    public function getModelDataById($ajaxReturn = true)
    {
        $info = parent::getModelDataById(false);
        if (!empty($info["openid"]) && empty($info["student_id"])) {
            $studentInfo = M('student')->where(array("token" => get_token(), "openid" => $info["openid"]))->find();
            $info["student_id"] = $studentInfo["id"];
        }
        $info["total_fee"] = number_format(intval($info["total_fee"]) / 100, 2, ".", "");
        $this->ajaxReturn($info);
    }


    /**
     * admin save
     */
    public function  saveAdmin()
    {
        $user = session('user_auth');
        $_POST["user_id"] = $user["uid"];
        $_POST["total_fee"] = strval($_POST["total_fee"] * 100);
        $_POST["result_code"] = "SUCCESS";
        $_POST["pay_channel"] = "human";
        parent::saveAdmin();
    }

//    /**
//     * 首页资金统计
//     */
//    public function payStatics()
//    {
//        $result = array();
//        $Model = M("eo2o_payment");
//
//        // search
//        $timeType = i("timetype");
//
//        $sql = "call statics_type_pay('" . get_token() . "','" . $timeType . "');";
//        $result["typePay"] = $Model->query($sql);
//        $db = $Model->db();
////        $result["typePay"]->close();
////        $db->queryID -> close();
////        $db->free();
////        $db->next_result();
//        $sql = "call statics_date_pay('" . get_token() . "','" . $timeType . "');";
//        $result["datePay"] = M("eo2o_payment")->query($sql);
//
//        // return
//            $this->success("成功", null, null, $result);
//    }

}
