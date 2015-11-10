<?php

namespace Addons\School\Controller;

/**
 * Class PaymentController
 * @package Addons\School\Controller
 */
class PaymentController extends SchoolBaseController{
    function paybegin() {
        $this->model = $this->getModel ( 'student' );
        $student =  M('student')->where('openid="'.get_openid().'"')->find();

        parent::_initialize ();
    }

    /**
     * reveive the pay notification
     */
    function payover(){
        $attach  = i('attach');
        $out_trade_no = i('out_trade_no');
    }
}
