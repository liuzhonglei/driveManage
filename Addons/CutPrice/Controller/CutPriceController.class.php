<?php

namespace Addons\CutPrice\Controller;

/**
 * Class CutPriceController
 * @package Addons\CutPrice\Controller
 */
class CutPriceController extends BaseController {
    /**
     * list the data
     */
    function lists(){
        $this->model = $this->getModel ( 'cut_price_count' );
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        parent::common_lists($this->model,0,"lists", $order = 'time_sign desc');

    }

    /**
     * list the data count
     */
    function listsDetail(){
        $this->model = $this->getModel ( 'cut_price' );
        $this->listsTable = "cut_price_all";
        $_POST['token'] = get_token();
        $this->assign('add_button',0);
//        $this->assign('del_button',0);
        parent::common_lists($this->model,0,"lists", $order = 'id desc');
    }



}
