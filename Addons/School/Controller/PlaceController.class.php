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

    /**
     * 初始化
     */
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
        //取得查询token
        $query_token = $_REQUEST['query_token'];
        $map = array('is_qingqing' => '1');
        if (!empty($query_token)) {
            $map['wp_school_place.token'] = $query_token;
        }

        // 查找数据
        $data = M('school_place')->field('wp_school_place.*,wp_school.name school_name')->join('wp_school on wp_school.token = wp_school_place.token')->where($map)->select();

        // 返回
        $this->ajaxReturn($data);
    }
}