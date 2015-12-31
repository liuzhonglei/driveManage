<?php

namespace Addons\CutPrice\Controller;


use Addons\School\Controller\Common\Util\DataBaseUtil;
use Addons\School\Controller\Common\Util\CommonUtil;
use Addons\School\Controller\Common\Util\RequestUtil;



/**
 * Class CutPriceController
 * @package Addons\CutPrice\Controller
 */
class CutPriceController extends BaseController {

    /**
     * init
     */
    function _initialize()
    {
        parent::_initialize();
        $this->model = $this->getModel('cut_price_count');
        $this->dataMultiEdit = true;

    }


    /**
     * list the data
     */
    function lists(){
        $this->model = $this->getModel ( 'cut_price_count' );
        $this->assign('add_button',0);
        $this->assign('del_button',0);

        // get data
        $list_data = $this->_get_model_list($this->model,0,$order = 'time desc');

        // configure data
        $this->assign($list_data);

        // display
        $this->display("lists");
    }

    /**
     * list the data count
     */
    function listsDetail(){
        $this->model = $this->getModel ( 'cut_price' );
//        $this->listsTable = "cut_price";
        $_POST['token'] = get_token();
        $this->assign('add_button',0);
        parent::common_lists($this->model,0,"lists", $order = 'id desc');
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
        if($model['name'] == 'cut_price'){
            $list_data = parent::_get_model_list($model,$page,$order);
            $list_data ['list_data'] = $this->convertListField($list_data ['list_data'], 'openid', 'name', 'student', 'openid');
            $list_data ['list_data'] = $this->convertListField($list_data ['list_data'], 'openid', 'nickname', 'follow', 'openid');
            return $list_data;
        }

        // TODO 修改排序
        $order = "";

        // 返回数据
        $list_data = $this->_list_grid($model);


        // 生成查询条件
        $map = "token = '" . get_token()."'";
        $fieldValue = RequestUtil::getComplexQueryText();
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

        // 查询数目
        $count = DataBaseUtil::getDataCount( $this->getTableName(),$map);
        $list_data ['count'] = $count;
        if($count < $row){
            $page = 1;
        }



        // 查询数据
        $tableName = $this->getTableName();
        $sql = $this->createSql($map, $page, $row, $order);
        $data = M($tableName)->query($sql);
        $list_data ['list_data'] = $data;



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
    private function createSql($map, $page, $row, $order)
    {
        // 条件语句
        $mapSql = DataBaseUtil::createMapSql($map);

        // 限制字符
        $limitSql = DataBaseUtil::createLimitSql($page,$row);

        // 排序语句
        $orderSql = DataBaseUtil::createOrderSql($order);

        // 组装语句
        $sql = <<<STR
SELECT
	*, (
		SELECT
			nickname
		FROM
			wp_follow
		WHERE
			tw.openid = openid
		AND tw.token = token
		LIMIT 1
	) AS nickname
FROM
	(
		SELECT
		    distinct
			t.id,
			t.token,
			t.openid,
			t.count,
			t.fee,
			t1.name,
			t1.phone,
			t1.remark,
			t1.time_sign
		FROM
			(
				SELECT
					*
				FROM
					wp_cut_price_count_detail
				$mapSql
                $orderSql
	            $limitSql
			) t
		LEFT JOIN wp_student t1 ON t.openid = t1.openid
		AND t.token = t1.token

	) tw
	;
STR;

        // return
        return $sql;
    }
}
