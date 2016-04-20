<?php

namespace Addons\Student\Model;

use Think\Model;

/**
 * Student模型
 */
class StudentModel extends Model
{
    // 表名
    protected $tableName = 'student';


    /**
     * 增加学员后,删除数据
     * @param $data
     * @param $options
     */
    protected function _after_insert($data, $options)
    {
        // 查找课程信息
        $course = M("school_course")->where(array("id" => $data["course_id"], "token" => get_token()))->find();

        /**
         * 状态为初始化,并且学时为0
         */
        if (intval($data["status"]) < 1 && intval($data["status"]) > -99 && empty($data["time_k1"])) {
            // 查询报名费
            if (!empty($course) && !empty($course["learn_pay_item_id"])) {
                // 查找支付项目
                $payItem = M("school_payitem")->where(array("id" => $course["learn_pay_item_id"], "token" => get_token()))->find();

                // 查找已支付项目
                $map = array("token" => get_token(), "student_id" => $data["id"], "payitem_id" => $course["learn_pay_item_id"]);
                if (!empty($payItem) && empty(M("eo2o_payment")->where($map)->find())) {
                    $map["out_trade_no"] = $this->time();
                    $map["result_code"] = "WAIT";
                    $map["total_fee"] = $payItem["fee"] * 100;
                    M("eo2o_payment")->add($map);
                }
            }

            // 查找报名费2期
            if (!empty($course) && !empty($course["learn_pay_item_id_2"])) {
                $payItem = M("school_payitem")->where(array("id" => $course["learn_pay_item_id_2"], "token" => get_token()))->find();

                // 查找已支付项目
                $map = array("token" => get_token(), "student_id" => $data["id"], "payitem_id" => $course["learn_pay_item_id_2"]);
                if (!empty($payItem) && empty(M("eo2o_payment")->where($map)->find())) {
                    $map["out_trade_no"] = $this->time();
                    $map["result_code"] = "WAIT";
                    $map["total_fee"] = $payItem["fee"] * 100;
                    M("eo2o_payment")->add($map);
                }
            }

            // 学杂费
            if (!empty($course) && !empty($course["tuition_id"])) {
                $payItem = M("school_payitem")->where(array("id" => $course["tuition_id"], "token" => get_token()))->find();

                // 查找已支付项目
                $map = array("token" => get_token(), "student_id" => $data["id"], "payitem_id" => $course["tuition_id"]);
                if (!empty($payItem) && empty(M("eo2o_payment")->where($map)->find())) {
                    $map["out_trade_no"] = $this->time();
                    $map["result_code"] = "WAIT";
                    $map["total_fee"] = $payItem["fee"] * 100;
                    M("eo2o_payment")->add($map);
                }
            }
        }
    }

    /**
     * 修改信息后
     * @param $data
     * @param $options
     */
    protected function _after_update($data, $options)
    {
        $course = M("school_course")->where(array("id" => $data["course_id"], "token" => get_token()))->find();

        // 查询报名费
        if (!empty($course) && $data["belong"] = "OUT" && !empty($course["link_charge_item_id"])) {
            // 查找支付项目
            $payItem = M("school_payitem")->where(array("id" => $course["link_charge_item_id"], "token" => get_token()))->find();

            // 查找已支付项目
            $map = array("token" => get_token(), "student_id" => $data["id"], "payitem_id" => $course["link_charge_item_id"]);
            if (!empty($payItem) && empty(M("eo2o_payment")->where($map)->find())) {
                $map["out_trade_no"] = $this->time();
                $map["result_code"] = "WAIT";
                $map["total_fee"] = $payItem["fee"] * 100;
                M("eo2o_payment")->add($map);
            }
        }
    }
}
