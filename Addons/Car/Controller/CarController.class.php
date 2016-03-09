<?php
namespace Addons\Car\Controller;

use Addons\School\Controller\BaseController;

/**
 * the main school controller
 */
class CarController extends BaseController
{
    /**
     * 初始化
     */
    function _initialize()
    {
        $this->model = $this->getModel('car');
        $this->dataMultiEdit = true;

        parent::_initialize();
    }

    /**
     * 查询车
     * @param null $model
     * @param int $page
     * @param null $order
     * @param null $map
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = null, $map = null)
    {
        $result = parent::_get_model_list($model, $page, $order, $map);
        foreach ($result["list_data"] as &$item) {
            $teacherList = M('teacher')->where(array("car_id" => $item["id"]))->select();
            foreach ($teacherList as $teacher) {
                if (empty($item["teacher"])) {
                    $item["teacher"] = "";
                }
                $item["teacher"] .= " " . $teacher["name"];
            }
        }
        return $result;
    }
}
