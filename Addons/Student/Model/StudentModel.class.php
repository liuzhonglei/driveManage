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
//    protected function _after_insert($data, $options)
//    {
//        /**
//         * 状态为初始化,并且学时为0
//         */
//        if (intval($data["status"]) <= 1 && intval($data["status"]) > -99 && empty($data["time_k1"])) {
//            // 查询报名费
//            $course = M("school_course")->where(array("id" => $data["course_id"], "token" => get_token()))->find();
//            if (!empty($course) && !empty($course["learn_pay_item_id"])) {
//                $payItem = M("school_payitem")->where(array("id" => $course["learn_pay_item_id"], "token" => get_token()))->find();
//
//                // 保存报名费
//                if (!empty($payItem)) {
//                    M("eo2o_payment")->add(array( "token" => get_token(), "student_id" => $data["id"], "pay_item_id" => $course["learn_pay_item_id"], "total_fee" => $payItem["fee"]*100));
//                }
//            }
//        }
//    }
}
