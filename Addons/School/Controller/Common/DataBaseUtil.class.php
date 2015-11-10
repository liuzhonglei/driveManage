<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/11/10
 * Time: 10:19
 */
namespace Addons\School\Controller\Common;


class DataBaseUtil {
    /**
     * 查询微信粉丝信息
     * @param $text 查询字符
     * @param $fieldResult 查询字段
     * @return mixed 查询结果
     */
    public static function queryFollowValue($text, $fieldResult)
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
     * 查询学员信息
     * @param $text 查询字符串
     * @param $fieldResult 查询字段
     * @return mixed 数据
     */
    public static function queryStudentValue($text, $fieldResult)
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
     * 计算数据数目
     * @param $tableName 表名
     * @param $map 条件
     * @return false|int 数目
     */
    public static function getDataCount($tableName, $map){
        $count = M($tableName)->execute("select count(*) from wp_" . $tableName . " t  where " . $map);
        return $count;
    }

    /**
     * 创建条件语句
     * @param $map
     * @return string
     */
    public  static function createMapSql($map){
        $mapSql = "";
        if (is_array($map)) {
            foreach ($map as $key => $value) {
                if (!empty($mapSql)) {
                    $mapSql .= " and ";
                }
                $mapSql .= $key . " = '" . $value . "'";
            }
        } else {
            $mapSql = $map;
        }
        if (!empty($mapSql)) {
            $mapSql = "where " . $mapSql;
        }
        return $mapSql;
    }

    /**
     * 创建排序语句
     * @param $order
     * @return string
     */
    public static function createOrderSql($order){
        $orderSql = $order;
        if(!empty($orderSql)){
            $orderSql = "order by ".$order;
        }
        return $orderSql;
    }

    /**
     * 创建数据查询语句
     * @param $page
     * @param $row
     * @return string
     */
    public static  function createLimitSql($page, $row){
        $limitSql = "";
        if (!empty($page) && !empty($row)) {
            $limitBegin = ($page - 1) * $row;
            $limitSql = "limit " . $limitBegin . "," . $row;
        }
        return $limitSql;
    }
}