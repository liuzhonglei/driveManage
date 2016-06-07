<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 17:17
 */

namespace Addons\School\Controller;


class PlaceController extends SchoolBaseController
{

    function _initialize()
    {
        parent::_initialize();
        $this->model = $this->getModel('school_place');
        $this->dataMultiEdit = true;

    }

    /**
     * 显示所有的驾校
     */
    function listAllPlace()
    {
        // 查找数据
        $data = M('school_place')->field('wp_school_place.*,wp_school.name school_name')->join('wp_school on wp_school.token = wp_school_place.token')->where(array('is_qingqing' => '1'))->select();


        $this->ajaxReturn($data);
    }

}