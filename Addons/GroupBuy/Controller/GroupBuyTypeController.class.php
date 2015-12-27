<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/12/20
 * Time: 10:40
 */

namespace Addons\GroupBuy\Controller;


class GroupBuyTypeController extends BaseController {

    /**
     * init
     */
    function _initialize()
    {
        parent::_initialize();

        // 配置模型
        $this->model = $this->getModel('groupbuy_type');
    }

    /**
     * 扩展字段下拉框
     * @param $fields
     * @return \Addons\School\Controller\Common\Controller\当前的字段
     */
    public function getFieldList($fields)
    {
        $fields = parent::getFieldList($fields);
        $fields = $this->setFiledExtra($fields, "payitem_id", 'school_payitem', 'name');

        // 返回
        return $fields;
    }
}