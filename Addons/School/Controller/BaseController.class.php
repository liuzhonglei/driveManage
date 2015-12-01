<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\School\Controller;

require_once 'Excel/PHPExcel.php';
require_once 'Excel/PHPExcel/Reader/Excel2007.php';
require_once 'Excel/PHPExcel/Reader/Excel5.php';
include_once 'Excel/PHPExcel/IOFactory.php';
include_once 'Excel/PHPExcel/style/Alignment.php';
include_once 'Excel/PHPExcel/style/NumberFormat.php';
require_once(dirname(__FILE__) . '/../../QingQing/Public/Weixin/JSSDK.php');

use Addons\School\Controller\Common\Controller\AdminController;
use Think\Model;

define ('MOBILE_PUBLIC_PATH', __ROOT__ . '/Addons/School/View/default/Mobile');
define ('MOBILE_PATH', "Addons://School@Mobile/");

define ('QINGQING_TOKEN', "gh_94ecad95d624");

class BaseController extends AdminController
{
    // param
    var $listsTable;

    /**
     * get the school info
     * @return [type] [description]
     */
    protected function  getSchoolInfo()
    {
        $Model = M("school");
        $map = array('token' => get_token());
        $info = $Model->where($map)->find();
        return $info;
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
    private function getPayOpenid()
    {
        $payed = $_REQUEST['payed'];
        $result = "";
        if ($payed !== null) {
            $sql = "select openid from wp_eo2o_payment_count t where t.token = '" . get_token() . "' and  total_fee is not null";
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

}