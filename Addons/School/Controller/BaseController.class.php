<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

require_once dirname(__FILE__) . "/" . 'Excel/PHPExcel.php';
require_once dirname(__FILE__) . "/" . 'Excel/PHPExcel/Reader/Excel2007.php';
require_once dirname(__FILE__) . "/" . 'Excel/PHPExcel/Reader/Excel5.php';
include_once dirname(__FILE__) . "/" . 'Excel/PHPExcel/IOFactory.php';
include_once dirname(__FILE__) . "/" . 'Excel/PHPExcel/Style/Alignment.php';
include_once dirname(__FILE__) . "/" . 'Excel/PHPExcel/Style/NumberFormat.php';
require_once(dirname(__FILE__) . '/../../QingQing/Public/Weixin/JSSDK.php');

use Addons\School\Controller\Common\Controller\AdminController;
use Think\Model;

define('MOBILE_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Mobile');
define('MOBILE_PATH', "Addons://School@Mobile/");

define('QINGQING_TOKEN', "gh_94ecad95d624");

class BaseController extends AdminController
{
    // param
    var $listsTable;

    /**
     * 取得驾校信息
     * @return [type] [description]
     */
    protected function  getSchoolInfo()
    {
        $Model = M("school");
        $token = $_REQUEST['token'];
        $isAjax = $_REQUEST['isAjax'];
        if (empty($token)) {
            $token = get_token();
        }
        $map = array('token' => $token);
        $info = $Model->where($map)->find();

        // 查找封面信息
        if (!empty($info['photo'])) {
            $info['photo'] = get_cover($info['photo'])['path'];
        }

        // 返回
        if ($isAjax) {
            $this->ajaxReturn($info);
        } else {
            return $info;
        }
    }

    /**
     * 取得学员信息
     * @return mixed
     */
    protected function  getStudentInfo()
    {
        $Model = M("student");
        $map = array('token' => get_token(), 'openid' => get_openid());
        $info = $Model->where($map)->find();
        return $info;
    }

    /**
     * download the studentl lists
     */
    public function listsExcel()
    {
        $this->model ['list_row'] = 100000;
        // get data
        $list_data = $this->_get_model_list($this->model);
        $this->downloadExcel($list_data);
    }


    /**
     * search the follow
     * @param $text
     * @param $fieldResult
     * @return mixed
     */
    public function getFollowOpneids($text, $fieldResult)
    {
        $fields = array("nickname");
        $sql = "select " . $fieldResult . " from wp_follow t where t.token = '" . get_token() . "' and ( ";
        $fieldMap = "";
        foreach ($fields as $field) {
            if (empty(!$fieldMap)) {
                $fieldMap .= "or ";
            }
            $fieldMap .= $field . " like '%" . $text . "%' ";
        };
        $sql .= $fieldMap . " )";
        return M('follow')->query($sql);
    }

    /**
     * get the
     * @return string
     */
    protected function getPayOpenid()
    {
        $payed = $_REQUEST['payed'];
        $result = "";
        if ($payed !== null) {
            $sql = "select openid from wp_eo2o_payment_count t where t.token = '" . get_token() . "' and in_or_out = 'IN' and  total_fee is not null";
            $records = M('eo2o_payment_count')->query($sql);
            if (count($records) > 0) {
                if ($payed) {
                    $result = "and openid in ( " . $this->getArrayStr($records) . " )";
                } else {
                    $result = "and openid not in ( " . $this->getArrayStr($records) . " )";
                }
            } else {
                if ($payed) {
                    $result = "1 = 2";
                } else {
                    $result = " ";
                }
            }

        }

        // return
        return $result;

    }

    /**
     * search the students
     * @param $text
     * @param $fieldResult
     * @return mixed
     */
    public function getStudentOpenids($text, $fieldResult)
    {
        $fields = array("name", "phone");
        $sql = "select " . $fieldResult . " from wp_student t where t.token = '" . get_token() . "' and (";
        $fieldMap = "";
        foreach ($fields as $field) {
            if (empty(!$fieldMap)) {
                $fieldMap .= "or ";
            }
            $fieldMap .= $field . " like '%" . $text . "%' ";
        };
        $sql .= $fieldMap . " )";
        return M('student')->query($sql);
    }

    /**
     * convert  the records to condition str
     * @param $array
     * @return string
     */
    protected function  getArrayStr($array, $quotation = true)
    {
        $return = "";
        foreach ($array as $record) {
            if (!empty($return)) {
                $return .= ",";
            }
            foreach ($record as $key => $value) {
                if ($quotation) {
                    $return .= "'" . $value . "'";
                } else {
                    $return .= $value;

                }
                break;
            }

        }
        return $return;
    }


}