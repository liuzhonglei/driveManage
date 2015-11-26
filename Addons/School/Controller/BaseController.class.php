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


}