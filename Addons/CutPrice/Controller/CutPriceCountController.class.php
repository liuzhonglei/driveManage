<?php

namespace Addons\CutPrice\Controller;

use Addons\School\Controller\Common\DataBaseUtil;
use Addons\School\Controller\Common\CommonUtil;


/**
 * Class CutPriceCountController
 * @package Addons\CutPrice\Controller
 */
class CutPriceCountController extends BaseController
{
    /**
     * 数据库模型
     */
    var $model;

    /**
     * 初始化
     */
    function _initialize()
    {
        $this->model = $this->getModel('wp_cut_price_count_detail');
        parent::_initialize();
    }

    /**
     * 查询语句
     * @param null $model
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public function _get_model_list($model = null, $page = 0, $order = 'id desc')
    {
        // 生成查询条件
        $map = "t.token = '" . get_token();
        $fieldValue = "";
        foreach ($_REQUEST as $name => $value) {
            if (strpos($name, ',')) {
                $fieldValue = $value;
            }
        }
        if (!empty($fieldValue)) {
            $openidList1 = DataBaseUtil::queryFollowValue($fieldValue, "openid");
            $openidList2 = DataBaseUtil::queryStudentValue($fieldValue, "openid");
            $openidList = array_merge($openidList1, $openidList2);
            if (count($openidList) < 1) {
                $map .= " and 1 = 0";
            } else {
                $map .= " and openid in (" . CommonUtil::getArrayStr($openidList) . ")";
            }
        }

        // 关键字搜索
        $page || $page = I('p', 1, 'intval'); // 默认显示第一页数据
        $row = empty ($model ['list_row']) ? 20 : $model ['list_row'];
        empty ($fields) || in_array('id', $fields) || array_push($fields, 'id');

        // 查询数据
        $name = $this->getTableName();
        $sql = $this->createStudentSql($fields, $map, $page, $row, $order);
        $data = M($name)->query($sql);


        // 查询数目
        $count = M($name)->execute("select * from " . $name . " t  where " . $map);

        $list_data ['list_data'] = $data;
        $list_data ['count'] = $count;

        // 分页
        if ($count > $row) {
            $page = new \Think\Page ($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $list_data ['_page'] = $page->show();
        }

        // 返回
        return $list_data;
    }


    /**
     * 创建查询语句
     * @return string
     */
    private function createSql($fieldList, $map, $page, $row, $order)
    {
        // 字段自负
        $fieldsSql = implode(",", $fieldList);

        // 条件语句
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

        // 限制字符
        $limitSql = "";
        if (!empty($page) && !empty($row)) {
            $limitBegin = ($page - 1) * $row;
            $limitSql = "limit " . $limitBegin . "," . $row;
        }


        // 排序语句
        if(!$order){
            $order = "order by ".$order;
        }

        // 组装语句
        $sql = <<<STR
SELECT
	$fieldsSql
FROM
	(
		SELECT
			t.id,
			t.token,
			t.openid,
			t.count,
			t.fee,
			t1. NAME,
			t1.phone,
			t1.remark,
			t1.time_sign,
			(
				SELECT
					nickname
				FROM
					wp_follow
				WHERE
					t.openid = openid
				AND t.token = token
				LIMIT 1
			) AS nickname
		FROM
			(
				SELECT
					*
				FROM
					wp_cut_price_count_detail $mapSql $limitSql $order
			) t
		LEFT JOIN wp_student t1 ON t.openid = t1.openid
		AND t.token = t1.token;
) tw
STR;

        // return
        return $sql;

    }
}
