<?php

namespace Addons\Student\Model;

use Think\Model;

/**
 * Student模型
 */
class StudentModel extends Model
{
    protected $tableName = 'student';


    /**
     * 增加学员后,删除数据
     * @param $data
     * @param $options
     */
    protected function _after_insert($data, $options)
    {
        /**
         * 状态为初始化,并且学时为0
         */
        if (intval($data["status"]) < 1 && intval($data["status"]) > -99 && empty($data["time_k1"])) {
            // 查询报名费
            $course = M("school_course")->where(array("id" => $data["course_id"], "token" => get_token()))->find();
            if (!empty($course) && !empty($course["learn_pay_item_id"])) {
                // 查找支付项目
                $payItem = M("school_payitem")->where(array("id" => $course["learn_pay_item_id"], "token" => get_token()))->find();

                // 查找已支付项目
                $map = array("token" => get_token(), "student_id" => $data["id"], "payitem_id" => $course["learn_pay_item_id"]);
                if (!empty($payItem) && empty(M("eo2o_payment")->where($map)->find())) {
                    $map["result_code"] = "WAIT";
                    $map["total_fee"] = $payItem["fee"] * 100;
                    M("eo2o_payment")->add($map);
                }
            }
        }
    }
}
