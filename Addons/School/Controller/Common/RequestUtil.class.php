<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/10
 * Time: 11:51
 */

namespace Addons\School\Controller\Common;


class RequestUtil {

    /**
     * 得到复制查询对象(带逗号)
     * @return string
     */
    public static  function getComplexQueryText(){
        $fieldValue = "";
        foreach ($_REQUEST as $name => $value) {
            if (strpos($name, ',')) {
                $fieldValue = $value;
            }
        }

        return $fieldValue;
    }

    /**
     * 显示页
     * @param $page
     * @return mixed|string
     */
    public static function  getPage($page){
        $page || $page = I('p', 1, 'intval'); // 默认显示第一页数据
        return $page;
    }

    /**
     * 每页显示个数
     * @param $model 数据模型
     * @return int
     */
    public static  function getRow($model){
        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];
        return $row;
    }

    /**
     * 设置分页显示配置
     * @param $count
     * @param $row
     * @return string
     */
    public  static function  getPageShow($count,$row){
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            return $page->show();
        }
        return null;
    }
}